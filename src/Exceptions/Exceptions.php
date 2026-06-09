<?php

declare(strict_types=1);

namespace BaleBot\Exceptions;

use RuntimeException;

class BaleException extends RuntimeException {}

class HttpException extends BaleException
{
    public function __construct(
        string $message,
        private readonly int $httpCode = 0,
        private readonly ?array $responseBody = null,
    ) {
        parent::__construct($message, $httpCode);
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getResponseBody(): ?array
    {
        return $this->responseBody;
    }
}

class ApiException extends BaleException
{
    public function __construct(
        string $message,
        private readonly int $errorCode,
        private readonly ?string $description = null,
    ) {
        parent::__construct($message, $errorCode);
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}

class InvalidTokenException extends BaleException {}

class WebhookException extends BaleException {}
