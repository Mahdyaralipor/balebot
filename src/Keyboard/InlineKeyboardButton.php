<?php
declare(strict_types=1);
namespace BaleBot\Keyboard;
class InlineKeyboardButton {
    private function __construct(
        private readonly string $text,
        private readonly ?string $callbackData = null,
        private readonly ?string $url = null,
    ) {}
    public static function callback(string $text, string $data): self { return new self($text, callbackData: $data); }
    public static function url(string $text, string $url): self { return new self($text, url: $url); }
    public function toArray(): array {
        $data = ['text' => $this->text];
        if ($this->callbackData !== null) $data['callback_data'] = $this->callbackData;
        if ($this->url !== null)          $data['url']           = $this->url;
        return $data;
    }
}
