<?php
declare(strict_types=1);
namespace BaleBot\Types;
class PhotoSize {
    public readonly string $fileId;
    public readonly string $fileUniqueId;
    public readonly int $width;
    public readonly int $height;
    public readonly ?int $fileSize;
    private function __construct(array $data) {
        $this->fileId       = $data['file_id'];
        $this->fileUniqueId = $data['file_unique_id'];
        $this->width        = $data['width'];
        $this->height       = $data['height'];
        $this->fileSize     = $data['file_size'] ?? null;
    }
    public static function fromArray(array $data): self { return new self($data); }
}
