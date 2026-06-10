<?php
declare(strict_types=1);
namespace BaleBot\Keyboard;
class ReplyKeyboardButton {
    public function __construct(
        private readonly string $text,
        private readonly bool $requestContact = false,
        private readonly bool $requestLocation = false,
    ) {}
    public static function make(string $text): self { return new self($text); }
    public function withContact(): self { return new self($this->text, requestContact: true); }
    public function withLocation(): self { return new self($this->text, requestLocation: true); }
    public function toArray(): array {
        $data = ['text' => $this->text];
        if ($this->requestContact)  $data['request_contact']  = true;
        if ($this->requestLocation) $data['request_location'] = true;
        return $data;
    }
}
