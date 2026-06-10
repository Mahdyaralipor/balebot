<?php
declare(strict_types=1);
namespace BaleBot\Keyboard;
class ReplyKeyboardMarkup {
    private array $keyboard = [];
    private bool $resizeKeyboard = true;
    private bool $oneTimeKeyboard = false;
    public function row(ReplyKeyboardButton ...$buttons): self { $this->keyboard[] = $buttons; return $this; }
    public function resize(bool $v = true): self { $this->resizeKeyboard = $v; return $this; }
    public function oneTime(bool $v = true): self { $this->oneTimeKeyboard = $v; return $this; }
    public function toArray(): array {
        return [
            'keyboard'          => array_map(fn($row) => array_map(fn($btn) => $btn->toArray(), $row), $this->keyboard),
            'resize_keyboard'   => $this->resizeKeyboard,
            'one_time_keyboard' => $this->oneTimeKeyboard,
        ];
    }
}
