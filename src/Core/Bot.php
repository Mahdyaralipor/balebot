<?php

declare(strict_types=1);

namespace BaleBot\Core;

use BaleBot\Contracts\BotInterface;
use BaleBot\Contracts\HandlerInterface;
use BaleBot\Contracts\HttpClientInterface;
use BaleBot\Contracts\MiddlewareInterface;
use BaleBot\Exceptions\ApiException;
use BaleBot\Exceptions\InvalidTokenException;
use BaleBot\Http\CurlClient;
use BaleBot\Keyboard\InlineKeyboardMarkup;
use BaleBot\Keyboard\ReplyKeyboardMarkup;
use BaleBot\Keyboard\ReplyKeyboardRemove;
use BaleBot\Types\Message;
use BaleBot\Types\Update;
use BaleBot\Types\User;

class Bot implements BotInterface
{
    private const BASE_URL = 'https://tapi.bale.ai/bot';

    private readonly string $apiBase;
    private readonly HttpClientInterface $http;
    private readonly Dispatcher $dispatcher;

    public function __construct(
        private readonly string $token,
        ?HttpClientInterface $httpClient = null,
        ?Dispatcher $dispatcher = null,
    ) {
        if (empty($token)) {
            throw new InvalidTokenException('Bot token cannot be empty.');
        }

        $this->apiBase    = self::BASE_URL . $token;
        $this->http       = $httpClient ?? new CurlClient();
        $this->dispatcher = $dispatcher ?? new Dispatcher();
    }

    // ── Fluent handler registration (delegates to dispatcher) ─────────────

    public function onCommand(string $command, HandlerInterface|callable $handler): static
    {
        $this->dispatcher->onCommand($command, $handler);
        return $this;
    }

    public function onMessage(HandlerInterface|callable $handler): static
    {
        $this->dispatcher->onMessage($handler);
        return $this;
    }

    public function onCallbackQuery(HandlerInterface|callable $handler): static
    {
        $this->dispatcher->onCallbackQuery($handler);
        return $this;
    }

    public function onFallback(HandlerInterface|callable $handler): static
    {
        $this->dispatcher->onFallback($handler);
        return $this;
    }

    public function use(MiddlewareInterface $middleware): static
    {
        $this->dispatcher->middleware($middleware);
        return $this;
    }

    // ── BotInterface API methods ───────────────────────────────────────────

    public function getMe(): User
    {
        $data = $this->call('getMe');
        return User::fromArray($data['result']);
    }

    public function sendMessage(int|string $chatId, string $text, array $options = []): Message
    {
        $data = $this->call('sendMessage', array_merge([
            'chat_id' => $chatId,
            'text'    => $text,
        ], $this->normalizeKeyboard($options)));

        return Message::fromArray($data['result']);
    }

    public function sendPhoto(int|string $chatId, string $photo, array $options = []): Message
    {
        $isLocalFile = file_exists($photo);

        $params = array_merge(['chat_id' => $chatId], $options);

        if ($isLocalFile) {
            $data = $this->call('sendPhoto', $params, ['photo' => $photo]);
        } else {
            $data = $this->call('sendPhoto', array_merge($params, ['photo' => $photo]));
        }

        return Message::fromArray($data['result']);
    }

    public function sendDocument(int|string $chatId, string $document, array $options = []): Message
    {
        $params = array_merge(['chat_id' => $chatId], $options);

        if (file_exists($document)) {
            $data = $this->call('sendDocument', $params, ['document' => $document]);
        } else {
            $data = $this->call('sendDocument', array_merge($params, ['document' => $document]));
        }

        return Message::fromArray($data['result']);
    }

    public function sendAudio(int|string $chatId, string $audio, array $options = []): Message
    {
        $params = array_merge(['chat_id' => $chatId], $options);

        if (file_exists($audio)) {
            $data = $this->call('sendAudio', $params, ['audio' => $audio]);
        } else {
            $data = $this->call('sendAudio', array_merge($params, ['audio' => $audio]));
        }

        return Message::fromArray($data['result']);
    }

    public function sendVideo(int|string $chatId, string $video, array $options = []): Message
    {
        $params = array_merge(['chat_id' => $chatId], $options);

        if (file_exists($video)) {
            $data = $this->call('sendVideo', $params, ['video' => $video]);
        } else {
            $data = $this->call('sendVideo', array_merge($params, ['video' => $video]));
        }

        return Message::fromArray($data['result']);
    }

    public function editMessageText(int|string $chatId, int $messageId, string $text, array $options = []): Message
    {
        $data = $this->call('editMessageText', array_merge([
            'chat_id'    => $chatId,
            'message_id' => $messageId,
            'text'       => $text,
        ], $options));

        return Message::fromArray($data['result']);
    }

    public function deleteMessage(int|string $chatId, int $messageId): bool
    {
        $data = $this->call('deleteMessage', [
            'chat_id'    => $chatId,
            'message_id' => $messageId,
        ]);

        return (bool) $data['result'];
    }

    public function answerCallbackQuery(string $callbackQueryId, array $options = []): bool
    {
        $data = $this->call('answerCallbackQuery', array_merge([
            'callback_query_id' => $callbackQueryId,
        ], $options));

        return (bool) $data['result'];
    }

    public function setWebhook(string $url, array $options = []): bool
    {
        $data = $this->call('setWebhook', array_merge(['url' => $url], $options));
        return (bool) $data['result'];
    }

    public function deleteWebhook(): bool
    {
        $data = $this->call('deleteWebhook');
        return (bool) $data['result'];
    }

    public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 0): array
    {
        $data = $this->call('getUpdates', array_filter([
            'offset'  => $offset,
            'limit'   => $limit,
            'timeout' => $timeout,
        ]));

        return array_map(fn(array $u) => Update::fromArray($u), $data['result']);
    }

    // ── Update dispatching ────────────────────────────────────────────────

    public function handleUpdate(Update $update): void
    {
        $this->dispatcher->dispatch($update, $this);
    }

    /**
     * Auto-detect mode: webhook (from php://input) or long polling
     */
    public function run(): void
    {
        if ($this->isWebhookRequest()) {
            $this->handleWebhook();
        } else {
            $this->longPoll();
        }
    }

    public function handleWebhook(): void
    {
        $input = file_get_contents('php://input');

        if (empty($input)) {
            return;
        }

        $decoded = json_decode($input, true);

        if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            return;
        }

        $this->handleUpdate(Update::fromArray($decoded));

        if (!headers_sent()) {
            http_response_code(200);
        }
    }

    public function longPoll(int $timeout = 30): void
    {
        $offset = 0;

        while (true) {
            /** @var Update[] $updates */
            $updates = $this->getUpdates($offset, 100, $timeout);

            foreach ($updates as $update) {
                $this->handleUpdate($update);
                $offset = $update->updateId + 1;
            }
        }
    }

    // ── Internals ─────────────────────────────────────────────────────────

    private function call(string $method, array $params = [], array $files = []): array
    {
        $url = "{$this->apiBase}/{$method}";

        $response = empty($files)
            ? $this->http->post($url, $params)
            : $this->http->post($url, $params, $files);

        if (!($response['ok'] ?? false)) {
            throw new ApiException(
                $response['description'] ?? 'Unknown API error',
                $response['error_code'] ?? 0,
                $response['description'] ?? null,
            );
        }

        return $response;
    }

    private function isWebhookRequest(): bool
    {
        return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    private function normalizeKeyboard(array $options): array
    {
        if (!isset($options['reply_markup'])) {
            return $options;
        }

        $markup = $options['reply_markup'];

        if ($markup instanceof ReplyKeyboardMarkup
            || $markup instanceof InlineKeyboardMarkup
            || $markup instanceof ReplyKeyboardRemove
        ) {
            $options['reply_markup'] = json_encode($markup->toArray());
        }

        return $options;
    }
}
