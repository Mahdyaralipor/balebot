<?php
declare(strict_types=1);
namespace BaleBot\Exceptions;
class ApiException extends BaleException {
    public function __construct(string $message, private readonly int $errorCode, private readonly ?string $description = null) {
        parent::__construct($message, $errorCode);
    }
    public function getErrorCode(): int { return $this->errorCode; }
    public function getDescription(): ?string { return $this->description; }
}
