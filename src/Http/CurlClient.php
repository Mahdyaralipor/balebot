<?php

declare(strict_types=1);

namespace BaleBot\Http;

use BaleBot\Contracts\HttpClientInterface;
use BaleBot\Exceptions\HttpException;

final class CurlClient implements HttpClientInterface
{
    private const TIMEOUT = 30;
    private const CONNECT_TIMEOUT = 10;

    public function __construct(
        private readonly int $timeout = self::TIMEOUT,
        private readonly int $connectTimeout = self::CONNECT_TIMEOUT,
        private readonly ?string $proxy = null,
    ) {}

    /**
     * @throws HttpException
     */
    public function post(string $url, array $data = [], array $files = []): array
    {
        $ch = curl_init();

        $payload = empty($files)
            ? json_encode($data)
            : $this->buildMultipart($data, $files);

        $headers = empty($files)
            ? ['Content-Type: application/json']
            : [];

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        if ($this->proxy !== null) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        }

        return $this->execute($ch);
    }

    /**
     * @throws HttpException
     */
    public function get(string $url, array $query = []): array
    {
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        if ($this->proxy !== null) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        }

        return $this->execute($ch);
    }

    /**
     * @throws HttpException
     */
    private function execute(\CurlHandle $ch): array
    {
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new HttpException("cURL error: {$error}");
        }

        $decoded = json_decode((string) $response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new HttpException(
                'Invalid JSON response from Bale API',
                $httpCode
            );
        }

        if ($httpCode >= 400) {
            throw new HttpException(
                $decoded['description'] ?? 'HTTP error from Bale API',
                $httpCode,
                $decoded
            );
        }

        return $decoded;
    }

    private function buildMultipart(array $data, array $files): array
    {
        $payload = $data;

        foreach ($files as $key => $path) {
            $payload[$key] = new \CURLFile($path);
        }

        return $payload;
    }
}
