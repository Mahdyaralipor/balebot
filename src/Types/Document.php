<?php
declare(strict_types=1);
namespace BaleBot\Types;
class Document {
    public readonly string $fileId;
    public readonly string $fileUniqueId;
    public readonly ?string $fileName;
    public readonly ?string $mimeType;
    public readonly ?int $fileSize;
    private function __construct(array $data) {
        $this->fileId       = $data['file_id'];
        $this->fileUniqueId = $data['file_unique_id'];
        $this->fileName     = $data['file_name'] ?? null;
        $this->mimeType     = $data['mime_type'] ?? null;
        $this->fileSize     = $data['file_size'] ?? null;
    }
    public static function fromArray(array $data): self { return new self($data); }
}
