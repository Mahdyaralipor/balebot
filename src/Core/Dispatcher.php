<?php

declare(strict_types=1);

namespace BaleBot\Core;

use BaleBot\Contracts\BotInterface;
use BaleBot\Contracts\HandlerInterface;
use BaleBot\Contracts\MiddlewareInterface;
use BaleBot\Types\Update;

final class Dispatcher
{
    /** @var array<string, HandlerInterface|callable> */
    private array $commandHandlers = [];

    /** @var HandlerInterface|callable|null */
    private mixed $messageHandler = null;

    /** @var HandlerInterface|callable|null */
    private mixed $callbackHandler = null;

    /** @var HandlerInterface|callable|null */
    private mixed $fallbackHandler = null;

    /** @var MiddlewareInterface[] */
    private array $middlewares = [];

    public function onCommand(string $command, HandlerInterface|callable $handler): self
    {
        $this->commandHandlers[ltrim($command, '/')] = $handler;
        return $this;
    }

    public function onMessage(HandlerInterface|callable $handler): self
    {
        $this->messageHandler = $handler;
        return $this;
    }

    public function onCallbackQuery(HandlerInterface|callable $handler): self
    {
        $this->callbackHandler = $handler;
        return $this;
    }

    public function onFallback(HandlerInterface|callable $handler): self
    {
        $this->fallbackHandler = $handler;
        return $this;
    }

    public function middleware(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function dispatch(Update $update, BotInterface $bot): void
    {
        $pipeline = $this->buildPipeline($update, $bot);
        $pipeline();
    }

    private function buildPipeline(Update $update, BotInterface $bot): callable
    {
        $core = function () use ($update, $bot): void {
            $this->handle($update, $bot);
        };

        // Wrap middlewares in reverse order (last registered = outermost wrapper)
        $stack = array_reverse($this->middlewares);

        return array_reduce(
            $stack,
            fn(callable $next, MiddlewareInterface $mw) => fn() => $mw->process($update, $bot, $next),
            $core
        );
    }

    private function handle(Update $update, BotInterface $bot): void
    {
        // Command
        if ($update->isMessage()) {
            $command = $update->getCommand();

            if ($command !== null && isset($this->commandHandlers[$command])) {
                $this->call($this->commandHandlers[$command], $update, $bot);
                return;
            }

            if ($this->messageHandler !== null) {
                $this->call($this->messageHandler, $update, $bot);
                return;
            }
        }

        // Callback query
        if ($update->isCallbackQuery() && $this->callbackHandler !== null) {
            $this->call($this->callbackHandler, $update, $bot);
            return;
        }

        // Fallback
        if ($this->fallbackHandler !== null) {
            $this->call($this->fallbackHandler, $update, $bot);
        }
    }

    private function call(HandlerInterface|callable $handler, Update $update, BotInterface $bot): void
    {
        if ($handler instanceof HandlerInterface) {
            $handler->handle($update, $bot);
        } else {
            ($handler)($update, $bot);
        }
    }
}
