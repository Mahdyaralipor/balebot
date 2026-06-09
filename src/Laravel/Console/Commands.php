<?php

declare(strict_types=1);

namespace BaleBot\Laravel\Console;

use BaleBot\Core\Bot;
use Illuminate\Console\Command;

class WebhookSetCommand extends Command
{
    protected $signature   = 'balebot:webhook:set {url? : Webhook URL (defaults to APP_URL + route prefix)}';
    protected $description = 'Register the webhook with Bale API';

    public function handle(Bot $bot): int
    {
        $url = $this->argument('url')
            ?? rtrim(config('app.url'), '/') . '/' . ltrim(config('balebot.webhook.route_prefix', 'balebot/webhook'), '/');

        $this->info("Setting webhook to: {$url}");

        $bot->setWebhook($url);

        $this->info('✓ Webhook set successfully.');

        return self::SUCCESS;
    }
}

class WebhookDeleteCommand extends Command
{
    protected $signature   = 'balebot:webhook:delete';
    protected $description = 'Delete the registered webhook from Bale API';

    public function handle(Bot $bot): int
    {
        $bot->deleteWebhook();
        $this->info('✓ Webhook deleted.');

        return self::SUCCESS;
    }
}

class PollCommand extends Command
{
    protected $signature   = 'balebot:poll {--timeout=30 : Long polling timeout in seconds}';
    protected $description = 'Start long polling (for local development)';

    public function handle(Bot $bot): int
    {
        $timeout = (int) $this->option('timeout');

        $this->info("Starting long polling (timeout: {$timeout}s)… Press Ctrl+C to stop.");

        $bot->longPoll($timeout);

        return self::SUCCESS;
    }
}
