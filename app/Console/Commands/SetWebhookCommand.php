<?php

namespace App\Console\Commands;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use JsonException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

class SetWebhookCommand extends Command
{
    protected $signature = 'set:webhook';

    protected $description = 'Set webhook';

    /**
     * @throws TelegramException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function handle(): void
    {
        //$collectorBot = new Nutgram(config('services.telegram.collector_token'));
        //$collectorBot->setWebhook(config('app.url') . '/api/telegram/collector');

        $galleryBot = new Nutgram(config('services.telegram.gallery_token'));
        $galleryBot->setWebhook(config('app.url') . '/api/telegram/gallery');
    }
}
