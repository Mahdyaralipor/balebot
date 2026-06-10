# BaleBot PHP SDK

<p align="center">
  <a href="https://packagist.org/packages/balebot/balebot"><img src="https://img.shields.io/packagist/v/balebot/balebot" alt="Latest Version"></a>
  <a href="https://packagist.org/packages/balebot/balebot"><img src="https://img.shields.io/packagist/php-v/balebot/balebot" alt="PHP Version"></a>
  <a href="https://github.com/Mahdyaralipor/balebot/actions"><img src="https://github.com/Mahdyaralipor/balebot/workflows/CI/badge.svg" alt="CI Status"></a>
  <a href="https://codecov.io/gh/Mahdyaralipor/balebot"><img src="https://codecov.io/gh/Mahdyaralipor/balebot/branch/main/graph/badge.svg" alt="Coverage"></a>
  <a href="LICENSE"><img src="https://img.shields.io/packagist/l/balebot/balebot" alt="License"></a>
</p>

The most complete, type-safe PHP SDK for building [Bale Messenger](https://bale.ai) bots. Works as a standalone library or with Laravel.

---

## Features

- ✅ **Type-safe** — all API responses mapped to PHP 8.1+ readonly DTOs
- ✅ **Zero dependencies** — native cURL only; Guzzle optional
- ✅ **Laravel-first** — auto-discovery, Facade, Artisan commands, config
- ✅ **Flexible routing** — commands, messages, callbacks, fallback + middleware pipeline
- ✅ **Webhook & long polling** — auto-detected at runtime
- ✅ **PHP 8.1+** — readonly properties, named arguments, enums

---

## Installation

```bash
composer require Mahdyaralipor/balebot
```

### Laravel

Publish the config:

```bash
php artisan vendor:publish --tag=balebot-config
```

Add to `.env`:

```env
BALE_BOT_TOKEN=your-token-here
```

---

## Standalone Usage

```php
use BaleBot\Core\Bot;
use BaleBot\Keyboard\InlineKeyboardMarkup;
use BaleBot\Keyboard\InlineKeyboardButton;

$bot = new Bot(token: 'your-token');

$bot->onCommand('start', function ($update, $bot) {
    $keyboard = (new InlineKeyboardMarkup())
        ->row(
            InlineKeyboardButton::callback('خرید 🛒', 'action:buy'),
            InlineKeyboardButton::callback('راهنما ❓', 'action:help'),
        );

    $bot->sendMessage(
        $update->getChatId(),
        'به ربات خوش آمدید!',
        ['reply_markup' => $keyboard]
    );
});

$bot->onCallbackQuery(function ($update, $bot) {
    $bot->answerCallbackQuery($update->callbackQuery->id, ['text' => 'انجام شد ✓']);
});

$bot->run(); // auto-detects webhook or long polling
```

---

## Laravel Usage

```php
// routes/web.php — nothing needed, route is auto-registered

// AppServiceProvider::boot()
use BaleBot\Laravel\Facades\Bale;
use BaleBot\Keyboard\ReplyKeyboardMarkup;
use BaleBot\Keyboard\ReplyKeyboardButton;

Bale::onCommand('start', function ($update, $bot) {
    $keyboard = (new ReplyKeyboardMarkup())
        ->row(
            ReplyKeyboardButton::make('گزینه ۱'),
            ReplyKeyboardButton::make('گزینه ۲'),
        )
        ->resize();

    Bale::sendMessage(
        $update->getChatId(),
        'سلام! یک گزینه انتخاب کنید:',
        ['reply_markup' => $keyboard]
    );
});
```

### Artisan Commands

```bash
# Register webhook
php artisan balebot:webhook:set

# Register with a custom URL
php artisan balebot:webhook:set https://yourdomain.com/balebot/webhook

# Remove webhook
php artisan balebot:webhook:delete

# Start long polling (local development)
php artisan balebot:poll
php artisan balebot:poll --timeout=60
```

---

## Middleware

```php
use BaleBot\Contracts\MiddlewareInterface;
use BaleBot\Types\Update;
use BaleBot\Contracts\BotInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Update $update, BotInterface $bot, callable $next): void
    {
        $allowedIds = [123456, 789012];

        if (!in_array($update->getChatId(), $allowedIds)) {
            $bot->sendMessage($update->getChatId(), '⛔ دسترسی ندارید.');
            return;
        }

        $next();
    }
}

$bot->use(new AuthMiddleware());
```

---

## Keyboards

```php
use BaleBot\Keyboard\ReplyKeyboardMarkup;
use BaleBot\Keyboard\ReplyKeyboardButton;
use BaleBot\Keyboard\InlineKeyboardMarkup;
use BaleBot\Keyboard\InlineKeyboardButton;
use BaleBot\Keyboard\ReplyKeyboardRemove;

// Reply keyboard
$reply = (new ReplyKeyboardMarkup())
    ->row(ReplyKeyboardButton::make('گزینه ۱'), ReplyKeyboardButton::make('گزینه ۲'))
    ->row(ReplyKeyboardButton::make('بازگشت ↩️'))
    ->resize()
    ->oneTime();

// Inline keyboard
$inline = (new InlineKeyboardMarkup())
    ->row(
        InlineKeyboardButton::callback('تأیید ✅', 'confirm'),
        InlineKeyboardButton::callback('لغو ❌', 'cancel'),
    )
    ->row(
        InlineKeyboardButton::url('وب‌سایت 🌐', 'https://example.com'),
    );

// Remove keyboard
$remove = new ReplyKeyboardRemove();
```

---

## Type System

All API responses return strongly-typed objects:

```php
$message = $bot->sendMessage($chatId, 'سلام');
$message->messageId; // int
$message->chat->id;  // int
$message->from->fullName(); // string

$update->message->text;
$update->callbackQuery->data;
$update->getChatId(); // works for all update types
```

---

## Requirements

- PHP 8.1+
- `ext-curl`
- `ext-json`

**Optional:**
- Laravel 10 / 11 for framework integration
- `guzzlehttp/guzzle ^7.0` as alternative HTTP client

---

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) and open issues or pull requests on GitHub.

## License

MIT — see [LICENSE](LICENSE).
