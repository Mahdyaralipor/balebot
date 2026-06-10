<?php
declare(strict_types=1);
namespace BaleBot\Types;
class User {
    public readonly int $id;
    public readonly bool $isBot;
    public readonly string $firstName;
    public readonly ?string $lastName;
    public readonly ?string $username;
    private function __construct(array $data) {
        $this->id        = $data['id'];
        $this->isBot     = $data['is_bot'] ?? false;
        $this->firstName = $data['first_name'];
        $this->lastName  = $data['last_name'] ?? null;
        $this->username  = $data['username']  ?? null;
    }
    public static function fromArray(array $data): self { return new self($data); }
    public function fullName(): string { return trim($this->firstName . ' ' . ($this->lastName ?? '')); }
}
