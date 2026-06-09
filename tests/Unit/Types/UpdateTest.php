<?php

declare(strict_types=1);

namespace BaleBot\Tests\Unit\Types;

use BaleBot\Types\Update;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    public function test_it_parses_text_message(): void
    {
        $update = Update::fromArray($this->textMessagePayload());

        $this->assertTrue($update->isMessage());
        $this->assertFalse($update->isCallbackQuery());
        $this->assertSame('سلام', $update->message->text);
        $this->assertSame(123456789, $update->getChatId());
    }

    public function test_it_extracts_command(): void
    {
        $update = Update::fromArray($this->commandPayload('/start'));
        $this->assertSame('start', $update->getCommand());
    }

    public function test_it_extracts_command_with_botname(): void
    {
        $update = Update::fromArray($this->commandPayload('/start@MyBot'));
        $this->assertSame('start', $update->getCommand());
    }

    public function test_it_returns_null_command_for_plain_text(): void
    {
        $update = Update::fromArray($this->textMessagePayload());
        $this->assertNull($update->getCommand());
    }

    public function test_it_parses_callback_query(): void
    {
        $update = Update::fromArray($this->callbackQueryPayload());

        $this->assertTrue($update->isCallbackQuery());
        $this->assertSame('action:buy', $update->callbackQuery->data);
    }

    // ── Fixtures ──────────────────────────────────────────────────────────

    private function textMessagePayload(): array
    {
        return [
            'update_id' => 1,
            'message'   => [
                'message_id' => 100,
                'from'       => ['id' => 1, 'is_bot' => false, 'first_name' => 'علی'],
                'chat'       => ['id' => 123456789, 'type' => 'private', 'first_name' => 'علی'],
                'date'       => time(),
                'text'       => 'سلام',
            ],
        ];
    }

    private function commandPayload(string $command): array
    {
        $payload = $this->textMessagePayload();
        $payload['message']['text'] = $command;
        return $payload;
    }

    private function callbackQueryPayload(): array
    {
        return [
            'update_id'      => 2,
            'callback_query' => [
                'id'   => 'cq_1',
                'from' => ['id' => 1, 'is_bot' => false, 'first_name' => 'علی'],
                'data' => 'action:buy',
            ],
        ];
    }
}
