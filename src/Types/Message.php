<?php
declare(strict_types=1);
namespace BaleBot\Types;
class Message {
    public readonly int $messageId;
    public readonly ?User $from;
    public readonly Chat $chat;
    public readonly int $date;
    public readonly ?string $text;
    public readonly ?PhotoSize $photo;
    public readonly ?Document $document;
    public readonly ?Audio $audio;
    public readonly ?Video $video;
    private function __construct(array $data) {
        $this->messageId = $data['message_id'];
        $this->from      = isset($data['from'])     ? User::fromArray($data['from'])                : null;
        $this->chat      = Chat::fromArray($data['chat']);
        $this->date      = $data['date'];
        $this->text      = $data['text']     ?? null;
        $this->photo     = isset($data['photo'])    ? PhotoSize::fromArray(end($data['photo']))     : null;
        $this->document  = isset($data['document']) ? Document::fromArray($data['document'])        : null;
        $this->audio     = isset($data['audio'])    ? Audio::fromArray($data['audio'])              : null;
        $this->video     = isset($data['video'])    ? Video::fromArray($data['video'])              : null;
    }
    public static function fromArray(array $data): self { return new self($data); }
}
