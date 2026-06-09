<?php

declare(strict_types=1);

namespace BaleBot\Laravel;

use BaleBot\Contracts\BotInterface;
use BaleBot\Core\Bot;
use Illuminate\Support\ServiceProvider;

class BaleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/balebot.php', 'balebot');

        $this->app->singleton(Bot::class, function ($app) {
            $config = $app['config']['balebot'];

            return new Bot(
                token: $config['token'],
            );
        });

        $this->app->alias(Bot::class, BotInterface::class);
        $this->app->alias(Bot::class, 'balebot');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/balebot.php' => config_path('balebot.php'),
            ], 'balebot-config');

            $this->commands([
                Console\WebhookSetCommand::class,
                Console\WebhookDeleteCommand::class,
                Console\PollCommand::class,
            ]);
        }

        $this->registerWebhookRoute();
    }

    private function registerWebhookRoute(): void
    {
        if (!$this->app['config']->get('balebot.webhook.register_route', true)) {
            return;
        }

        $prefix = $this->app['config']->get('balebot.webhook.route_prefix', 'balebot/webhook');

        $this->app['router']->post($prefix, function () {
            /** @var Bot $bot */
            $bot = $this->app->make(Bot::class);
            $bot->handleWebhook();
        })->withoutMiddleware(['App\\Http\\Middleware\\VerifyCsrfToken', \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    }
}
