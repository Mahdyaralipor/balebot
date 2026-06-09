<?php

declare(strict_types=1);

namespace BaleBot\Types;

class Update
{
    public readonly int $updateId;
    public readonly ?Message $message;
    public readonly ?Message $editedMessage;
    public readonly ?CallbackQuery $callbackQuery;

    private function __construct(array $data)
    {
        $this->updateId      = $data['update_id'];
        $this->message       = isset($data['message'])        ? Message::fromArray($data['message'])             : null;
        $this->editedMessage = isset($data['edited_message']) ? Message::fromArray($data['edited_message'])      : null;
        $this->callbackQuery = isset($data['callback_query']) ? CallbackQuery::fromArray($data['callback_query']): null;
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function isMessage(): bool
    {
        return $this->message !== null;
    }

    public function isEditedMessage(): bool
    {
        return $this->editedMessage !== null;
    }

    public function isCallbackQuery(): bool
    {
        return $this->callbackQuery !== null;
    }

    /**
     * Returns the command name if the message starts with /
     * e.g. "/start foo" → "start"
     */
    public function getCommand(): ?string
    {
        $text = $this->message?->text ?? '';

        if (str_starts_with($text, '/')) {
            $parts = explode(' ', ltrim($text, '/'), 2);
            // Strip @BotName suffix if present
            return explode('@', $parts[0])[0];
        }

        return null;
    }

    public function getChatId(): int|string|null
    {
        return $this->message?->chat->id
            ?? $this->editedMessage?->chat->id
            ?? $this->callbackQuery?->message?->chat->id;
    }
}
