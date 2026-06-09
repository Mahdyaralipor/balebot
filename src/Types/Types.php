<?php

declare(strict_types=1);

namespace BaleBot\Types;

class Message
{
    public readonly int $messageId;
    public readonly ?User $from;
    public readonly Chat $chat;
    public readonly int $date;
    public readonly ?string $text;
    public readonly ?PhotoSize $photo;
    public readonly ?Document $document;
    public readonly ?Audio $audio;
    public readonly ?Video $video;
    public readonly ?ReplyKeyboardMarkup $replyMarkup;

    private function __construct(array $data)
    {
        $this->messageId   = $data['message_id'];
        $this->from        = isset($data['from'])   ? User::fromArray($data['from'])   : null;
        $this->chat        = Chat::fromArray($data['chat']);
        $this->date        = $data['date'];
        $this->text        = $data['text'] ?? null;
        $this->photo       = isset($data['photo'])    ? PhotoSize::fromArray(end($data['photo'])) : null;
        $this->document    = isset($data['document']) ? Document::fromArray($data['document'])    : null;
        $this->audio       = isset($data['audio'])    ? Audio::fromArray($data['audio'])          : null;
        $this->video       = isset($data['video'])    ? Video::fromArray($data['video'])          : null;
        $this->replyMarkup = null; // populated in outgoing messages only
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}

class User
{
    public readonly int $id;
    public readonly bool $isBot;
    public readonly string $firstName;
    public readonly ?string $lastName;
    public readonly ?string $username;

    private function __construct(array $data)
    {
        $this->id        = $data['id'];
        $this->isBot     = $data['is_bot'] ?? false;
        $this->firstName = $data['first_name'];
        $this->lastName  = $data['last_name'] ?? null;
        $this->username  = $data['username'] ?? null;
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function fullName(): string
    {
        return trim($this->firstName . ' ' . ($this->lastName ?? ''));
    }
}

class Chat
{
    public readonly int $id;
    public readonly string $type; // private | group | supergroup | channel
    public readonly ?string $title;
    public readonly ?string $username;
    public readonly ?string $firstName;
    public readonly ?string $lastName;

    private function __construct(array $data)
    {
        $this->id        = $data['id'];
        $this->type      = $data['type'];
        $this->title     = $data['title']      ?? null;
        $this->username  = $data['username']   ?? null;
        $this->firstName = $data['first_name'] ?? null;
        $this->lastName  = $data['last_name']  ?? null;
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function isPrivate(): bool
    {
        return $this->type === 'private';
    }

    public function isGroup(): bool
    {
        return in_array($this->type, ['group', 'supergroup'], true);
    }
}

class CallbackQuery
{
    public readonly string $id;
    public readonly User $from;
    public readonly ?Message $message;
    public readonly ?string $data;

    private function __construct(array $data)
    {
        $this->id      = $data['id'];
        $this->from    = User::fromArray($data['from']);
        $this->message = isset($data['message']) ? Message::fromArray($data['message']) : null;
        $this->data    = $data['data'] ?? null;
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}

class PhotoSize
{
    public readonly string $fileId;
    public readonly string $fileUniqueId;
    public readonly int $width;
    public readonly int $height;
    public readonly ?int $fileSize;

    private function __construct(array $data)
    {
        $this->fileId       = $data['file_id'];
        $this->fileUniqueId = $data['file_unique_id'];
        $this->width        = $data['width'];
        $this->height       = $data['height'];
        $this->fileSize     = $data['file_size'] ?? null;
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}

class Document
{
    public readonly string $fileId;
    public readonly string $fileUniqueId;
    public readonly ?string $fileName;
    public readonly ?string $mimeType;
    public readonly ?int $fileSize;

    private function __construct(array $data)
    {
        $this->fileId       = $data['file_id'];
        $this->fileUniqueId = $data['file_unique_id'];
        $this->fileName     = $data['file_name'] ?? null;
        $this->mimeType     = $data['mime_type'] ?? null;
        $this->fileSize     = $data['file_size'] ?? null;
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}

class Audio
{
    public readonly string $fileId;
    public readonly string $fileUniqueId;
    public readonly int $duration;
    public readonly ?string $performer;
    public readonly ?string $title;
    public readonly ?string $mimeType;
    public readonly ?int $fileSize;

    private function __construct(array $data)
    {
        $this->fileId       = $data['file_id'];
        $this->fileUniqueId = $data['file_unique_id'];
        $this->duration     = $data['duration'];
        $this->performer    = $data['performer'] ?? null;
        $this->title        = $data['title']     ?? null;
        $this->mimeType     = $data['mime_type'] ?? null;
        $this->fileSize     = $data['file_size'] ?? null;
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}

class Video
{
    public readonly string $fileId;
    public readonly string $fileUniqueId;
    public readonly int $width;
    public readonly int $height;
    public readonly int $duration;
    public readonly ?string $mimeType;
    public readonly ?int $fileSize;

    private function __construct(array $data)
    {
        $this->fileId       = $data['file_id'];
        $this->fileUniqueId = $data['file_unique_id'];
        $this->width        = $data['width'];
        $this->height       = $data['height'];
        $this->duration     = $data['duration'];
        $this->mimeType     = $data['mime_type'] ?? null;
        $this->fileSize     = $data['file_size'] ?? null;
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}
