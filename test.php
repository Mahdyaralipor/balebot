<?php

/**
 * BaleBot SDK — Manual Test Script
 * Run: php test.php
 */

require __DIR__ . '/vendor/autoload.php';

use BaleBot\Core\Bot;
use BaleBot\Keyboard\InlineKeyboardMarkup;
use BaleBot\Keyboard\InlineKeyboardButton;
use BaleBot\Keyboard\ReplyKeyboardMarkup;
use BaleBot\Keyboard\ReplyKeyboardButton;

// ─── Config ───────────────────────────────────────────────────────────────────
$token  = '772332535:myUuOZZEZnvjMxXOM8ShsTCecHd_sgh9Th0';  // توکن جدید از BotFather
$chatId = 170887652;  // آیدی عددی چت خودت
// ─────────────────────────────────────────────────────────────────────────────

$bot = new Bot(token: $token);

$passed = 0;
$failed = 0;

function test(string $name, callable $fn): void
{
    global $passed, $failed;
    echo str_pad($name, 45, '.');
    try {
        $fn();
        echo " ✓\n";
        $passed++;
    } catch (Throwable $e) {
        echo " ✗  ({$e->getMessage()})\n";
        $failed++;
    }
}

// ── 1. getMe ──────────────────────────────────────────────────────────────────
test('getMe returns User', function () use ($bot) {
    $me = $bot->getMe();
    assert($me->isBot === true, 'isBot should be true');
    assert(!empty($me->firstName), 'firstName should not be empty');
    echo "\n   → Bot name: {$me->firstName} (@{$me->username})";
});

// ── 2. sendMessage ────────────────────────────────────────────────────────────
test('sendMessage returns Message', function () use ($bot, $chatId) {
    $msg = $bot->sendMessage($chatId, '✅ تست پکیج BaleBot SDK');
    assert($msg->messageId > 0, 'messageId should be positive');
    assert($msg->chat->id === $chatId, 'chat id mismatch');
});

// ── 3. sendMessage with Reply Keyboard ───────────────────────────────────────
test('sendMessage with ReplyKeyboard', function () use ($bot, $chatId) {
    $keyboard = (new ReplyKeyboardMarkup())
        ->row(
            ReplyKeyboardButton::make('گزینه ۱ 🔵'),
            ReplyKeyboardButton::make('گزینه ۲ 🟢'),
        )
        ->row(ReplyKeyboardButton::make('بازگشت ↩️'))
        ->resize()
        ->oneTime();

    $msg = $bot->sendMessage(
        $chatId,
        'کیبورد Reply:',
        ['reply_markup' => $keyboard]
    );
    assert($msg->messageId > 0);
});

// ── 4. sendMessage with Inline Keyboard ──────────────────────────────────────
test('sendMessage with InlineKeyboard', function () use ($bot, $chatId) {
    $keyboard = (new InlineKeyboardMarkup())
        ->row(
            InlineKeyboardButton::callback('تأیید ✅', 'action:confirm'),
            InlineKeyboardButton::callback('لغو ❌', 'action:cancel'),
        )
        ->row(
            InlineKeyboardButton::url('وب‌سایت 🌐', 'https://bale.ai'),
        );

    $msg = $bot->sendMessage(
        $chatId,
        'کیبورد Inline:',
        ['reply_markup' => $keyboard]
    );
    assert($msg->messageId > 0);
});

// ── 5. editMessageText ────────────────────────────────────────────────────────
test('editMessageText works', function () use ($bot, $chatId) {
    $msg     = $bot->sendMessage($chatId, 'متن اولیه...');
    $edited  = $bot->editMessageText($chatId, $msg->messageId, 'متن ویرایش‌شده ✏️');
    assert($edited->messageId === $msg->messageId);
});

// ── 6. deleteMessage ─────────────────────────────────────────────────────────
test('deleteMessage works', function () use ($bot, $chatId) {
    $msg    = $bot->sendMessage($chatId, 'این پیام حذف می‌شه...');
    $result = $bot->deleteMessage($chatId, $msg->messageId);
    assert($result === true);
});

// ── 7. getUpdates ─────────────────────────────────────────────────────────────
test('getUpdates returns array', function () use ($bot) {
    $updates = $bot->getUpdates(limit: 1);
    assert(is_array($updates));
});

// ── 8. Type system — Update parsing ──────────────────────────────────────────
test('Update::fromArray parses correctly', function () {
    $update = \BaleBot\Types\Update::fromArray([
        'update_id' => 999,
        'message'   => [
            'message_id' => 1,
            'from'       => ['id' => 1, 'is_bot' => false, 'first_name' => 'علی'],
            'chat'       => ['id' => 100, 'type' => 'private'],
            'date'       => time(),
            'text'       => '/start',
        ],
    ]);

    assert($update->isMessage() === true);
    assert($update->getCommand() === 'start');
    assert($update->getChatId() === 100);
});

// ── Summary ───────────────────────────────────────────────────────────────────
echo "\n";
echo "─────────────────────────────────────────\n";
echo "  Passed: {$passed}  |  Failed: {$failed}\n";
echo "─────────────────────────────────────────\n";
