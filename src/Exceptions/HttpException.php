<?php
declare(strict_types=1);
namespace BaleBot\Exceptions;
class HttpException extends BaleException {
    public function __construct(string $message, private readonly int $httpCode = 0, private readonly ?array $responseBody = null) {
        parent::__construct($message, $httpCode);
    }
    public function getHttpCode(): int { return $this->httpCode; }
    public function getResponseBody(): ?array { return $this->responseBody; }
}
