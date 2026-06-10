<?php
declare(strict_types=1);
namespace BaleBot\Keyboard;
class InlineKeyboardMarkup {
    private array $keyboard = [];
    public function row(InlineKeyboardButton ...$buttons): self { $this->keyboard[] = $buttons; return $this; }
    public function toArray(): array {
        return ['inline_keyboard' => array_map(fn($row) => array_map(fn($btn) => $btn->toArray(), $row), $this->keyboard)];
    }
}
