<?php

namespace App\Http\Controllers;

use App\Telegram\Conversations\TestConversation;
use App\Telegram\InlineMenus\TestMenu;
use Illuminate\Support\Facades\Cache;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Nutgram\Configuration;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;

class TelegramGalleryController extends Controller
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke()
    {
        $bot = new Nutgram(config('services.telegram.gallery_token'), new Configuration(
            cache: Cache::store('redis')
        ));
        $bot->setRunningMode(Webhook::class);

        $bot->onCommand('start', function (Nutgram $bot) {
            $bot->sendMessage(text: 'Приветики!');
        });

        $bot->onCommand('test', TestMenu::class);

        $bot->run();
    }
}
