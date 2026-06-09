<?php

declare(strict_types=1);

namespace BaleBot\Contracts;

use BaleBot\Types\Update;

interface HandlerInterface
{
    public function handle(Update $update, BotInterface $bot): void;
}
