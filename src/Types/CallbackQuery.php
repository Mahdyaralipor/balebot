<?php
declare(strict_types=1);
namespace BaleBot\Types;
class CallbackQuery {
    public readonly string $id;
    public readonly User $from;
    public readonly ?Message $message;
    public readonly ?string $data;
    private function __construct(array $data) {
        $this->id      = $data['id'];
        $this->from    = User::fromArray($data['from']);
        $this->message = isset($data['message']) ? Message::fromArray($data['message']) : null;
        $this->data    = $data['data'] ?? null;
    }
    public static function fromArray(array $data): self { return new self($data); }
}
