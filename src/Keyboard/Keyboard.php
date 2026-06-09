<?php

declare(strict_types=1);

namespace BaleBot\Keyboard;

class ReplyKeyboardMarkup
{
    /** @var array<array<ReplyKeyboardButton>> */
    private array $keyboard = [];
    private bool $resizeKeyboard = true;
    private bool $oneTimeKeyboard = false;
    private bool $selective = false;

    public function row(ReplyKeyboardButton ...$buttons): self
    {
        $this->keyboard[] = $buttons;
        return $this;
    }

    public function resize(bool $resize = true): self
    {
        $this->resizeKeyboard = $resize;
        return $this;
    }

    public function oneTime(bool $oneTime = true): self
    {
        $this->oneTimeKeyboard = $oneTime;
        return $this;
    }

    public function selective(bool $selective = true): self
    {
        $this->selective = $selective;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'keyboard' => array_map(
                fn(array $row) => array_map(fn(ReplyKeyboardButton $btn) => $btn->toArray(), $row),
                $this->keyboard
            ),
            'resize_keyboard'    => $this->resizeKeyboard,
            'one_time_keyboard'  => $this->oneTimeKeyboard,
            'selective'          => $this->selective,
        ];
    }
}

class ReplyKeyboardButton
{
    public function __construct(
        private readonly string $text,
        private readonly bool $requestContact = false,
        private readonly bool $requestLocation = false,
    ) {}

    public static function make(string $text): self
    {
        return new self($text);
    }

    public function withContact(): self
    {
        return new self($this->text, requestContact: true);
    }

    public function withLocation(): self
    {
        return new self($this->text, requestLocation: true);
    }

    public function toArray(): array
    {
        $data = ['text' => $this->text];

        if ($this->requestContact) {
            $data['request_contact'] = true;
        }

        if ($this->requestLocation) {
            $data['request_location'] = true;
        }

        return $data;
    }
}

class ReplyKeyboardRemove
{
    public function toArray(): array
    {
        return ['remove_keyboard' => true];
    }
}

class InlineKeyboardMarkup
{
    /** @var array<array<InlineKeyboardButton>> */
    private array $keyboard = [];

    public function row(InlineKeyboardButton ...$buttons): self
    {
        $this->keyboard[] = $buttons;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'inline_keyboard' => array_map(
                fn(array $row) => array_map(fn(InlineKeyboardButton $btn) => $btn->toArray(), $row),
                $this->keyboard
            ),
        ];
    }
}

class InlineKeyboardButton
{
    private function __construct(
        private readonly string $text,
        private readonly ?string $callbackData = null,
        private readonly ?string $url = null,
    ) {}

    public static function callback(string $text, string $callbackData): self
    {
        return new self($text, callbackData: $callbackData);
    }

    public static function url(string $text, string $url): self
    {
        return new self($text, url: $url);
    }

    public function toArray(): array
    {
        $data = ['text' => $this->text];

        if ($this->callbackData !== null) {
            $data['callback_data'] = $this->callbackData;
        }

        if ($this->url !== null) {
            $data['url'] = $this->url;
        }

        return $data;
    }
}
