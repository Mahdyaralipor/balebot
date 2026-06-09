<?php

declare(strict_types=1);

namespace BaleBot\Laravel\Facades;

use BaleBot\Core\Bot;
use BaleBot\Types\Message;
use BaleBot\Types\User;
use Illuminate\Support\Facades\Facade;

/**
 * @method static User    getMe()
 * @method static Message sendMessage(int|string $chatId, string $text, array $options = [])
 * @method static Message sendPhoto(int|string $chatId, string $photo, array $options = [])
 * @method static Message sendDocument(int|string $chatId, string $document, array $options = [])
 * @method static Message editMessageText(int|string $chatId, int $messageId, string $text, array $options = [])
 * @method static bool    deleteMessage(int|string $chatId, int $messageId)
 * @method static bool    answerCallbackQuery(string $callbackQueryId, array $options = [])
 * @method static bool    setWebhook(string $url, array $options = [])
 * @method static bool    deleteWebhook()
 * @method static Bot     onCommand(string $command, callable $handler)
 * @method static Bot     onMessage(callable $handler)
 * @method static Bot     onCallbackQuery(callable $handler)
 *
 * @see Bot
 */
class Bale extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'balebot';
    }
}
