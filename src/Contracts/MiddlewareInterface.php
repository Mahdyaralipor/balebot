<?php

declare(strict_types=1);

namespace BaleBot\Contracts;

use BaleBot\Types\Update;

interface MiddlewareInterface
{
    /**
     * @param callable(Update, BotInterface): void $next
     */
    public function process(Update $update, BotInterface $bot, callable $next): void;
}
