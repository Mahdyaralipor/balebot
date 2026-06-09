<?php

declare(strict_types=1);

namespace BaleBot\Contracts;

use BaleBot\Exceptions\HttpException;

interface HttpClientInterface
{
    /**
     * @throws HttpException
     */
    public function post(string $url, array $data = [], array $files = []): array;

    /**
     * @throws HttpException
     */
    public function get(string $url, array $query = []): array;
}
