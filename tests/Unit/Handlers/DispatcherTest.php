<?php

declare(strict_types=1);

namespace BaleBot\Tests\Unit\Handlers;

use BaleBot\Contracts\BotInterface;
use BaleBot\Core\Dispatcher;
use BaleBot\Types\Update;
use PHPUnit\Framework\TestCase;

class DispatcherTest extends TestCase
{
    private BotInterface $bot;

    protected function setUp(): void
    {
        $this->bot = $this->createMock(BotInterface::class);
    }

    public function test_it_dispatches_command(): void
    {
        $called     = false;
        $dispatcher = new Dispatcher();
        $dispatcher->onCommand('start', function (Update $u, BotInterface $b) use (&$called) {
            $called = true;
        });

        $dispatcher->dispatch($this->makeCommandUpdate('/start'), $this->bot);

        $this->assertTrue($called);
    }

    public function test_it_ignores_unregistered_command(): void
    {
        $fallbackCalled = false;
        $dispatcher     = new Dispatcher();
        $dispatcher->onFallback(function () use (&$fallbackCalled) {
            $fallbackCalled = true;
        });

        $dispatcher->dispatch($this->makeCommandUpdate('/unknown'), $this->bot);

        $this->assertTrue($fallbackCalled);
    }

    public function test_middleware_is_called_before_handler(): void
    {
        $order      = [];
        $dispatcher = new Dispatcher();

        $dispatcher->middleware(new class($order) implements \BaleBot\Contracts\MiddlewareInterface {
            public function __construct(private array &$order) {}
            public function process(Update $u, BotInterface $b, callable $next): void {
                $this->order[] = 'middleware';
                $next();
            }
        });

        $dispatcher->onCommand('start', function () use (&$order) {
            $order[] = 'handler';
        });

        $dispatcher->dispatch($this->makeCommandUpdate('/start'), $this->bot);

        $this->assertSame(['middleware', 'handler'], $order);
    }

    public function test_middleware_can_stop_propagation(): void
    {
        $handlerCalled = false;
        $dispatcher    = new Dispatcher();

        $dispatcher->middleware(new class implements \BaleBot\Contracts\MiddlewareInterface {
            public function process(Update $u, BotInterface $b, callable $next): void {
                // intentionally NOT calling $next
            }
        });

        $dispatcher->onCommand('start', function () use (&$handlerCalled) {
            $handlerCalled = true;
        });

        $dispatcher->dispatch($this->makeCommandUpdate('/start'), $this->bot);

        $this->assertFalse($handlerCalled);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function makeCommandUpdate(string $command): Update
    {
        return Update::fromArray([
            'update_id' => 1,
            'message'   => [
                'message_id' => 1,
                'from'       => ['id' => 1, 'is_bot' => false, 'first_name' => 'Test'],
                'chat'       => ['id' => 1, 'type' => 'private'],
                'date'       => time(),
                'text'       => $command,
            ],
        ]);
    }
}
