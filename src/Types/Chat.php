<?php
declare(strict_types=1);
namespace BaleBot\Types;
class Chat {
    public readonly int $id;
    public readonly string $type;
    public readonly ?string $title;
    public readonly ?string $username;
    public readonly ?string $firstName;
    public readonly ?string $lastName;
    private function __construct(array $data) {
        $this->id        = $data['id'];
        $this->type      = $data['type'];
        $this->title     = $data['title']      ?? null;
        $this->username  = $data['username']   ?? null;
        $this->firstName = $data['first_name'] ?? null;
        $this->lastName  = $data['last_name']  ?? null;
    }
    public static function fromArray(array $data): self { return new self($data); }
    public function isPrivate(): bool { return $this->type === 'private'; }
    public function isGroup(): bool { return in_array($this->type, ['group', 'supergroup'], true); }
}
