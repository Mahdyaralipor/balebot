<?php
declare(strict_types=1);
namespace BaleBot\Keyboard;
class ReplyKeyboardRemove {
    public function toArray(): array { return ['remove_keyboard' => true]; }
}
