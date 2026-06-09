<?php

declare(strict_types=1);

namespace BaleBot\Contracts;

use BaleBot\Types\Message;
use BaleBot\Types\Update;
use BaleBot\Types\User;

interface BotInterface
{
    public function getMe(): User;

    public function sendMessage(int|string $chatId, string $text, array $options = []): Message;

    public function sendPhoto(int|string $chatId, string $photo, array $options = []): Message;

    public function sendDocument(int|string $chatId, string $document, array $options = []): Message;

    public function sendAudio(int|string $chatId, string $audio, array $options = []): Message;

    public function sendVideo(int|string $chatId, string $video, array $options = []): Message;

    public function editMessageText(int|string $chatId, int $messageId, string $text, array $options = []): Message;

    public function deleteMessage(int|string $chatId, int $messageId): bool;

    public function answerCallbackQuery(string $callbackQueryId, array $options = []): bool;

    public function setWebhook(string $url, array $options = []): bool;

    public function deleteWebhook(): bool;

    public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 0): array;

    public function handleUpdate(Update $update): void;

    public function run(): void;
}
