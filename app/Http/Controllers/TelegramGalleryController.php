<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Tag;
use App\Models\TelegramUser;
use App\Telegram\Commands\BrowseCommand;
use App\Telegram\Commands\RandomCommand;
use App\Telegram\Commands\RandomTagCommand;
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

        $bot->middleware(function (Nutgram $bot, $next) {
            if (!$bot->userId()) {
                $bot->sendMessage('Что-то не так...');
                return;
            }

            $user = TelegramUser::firstOrCreate(['id' => $bot->userId()]);

            $bot->set('user', $user);

            $next($bot);
        });

        $bot->onCommand('start', function (Nutgram $bot) {
            $user = $bot->get('user');
            $bot->sendMessage(text: collect([
                "Приветик! 🥰",
                "==============================",
                "🏞 Изображений в базе: " . Image::count(),
                "⭐️ Тегов в базе: " . Tag::count(),
                "==============================",
                ($user->safe_mode ? 'Безопасный режим включен (выключить /unsafe).' : 'Безопасный режим выключен (включить /safe).'),
                'Просматривать галерею можно командой >> /browse',
                'Получить случайное изображение >> /random',
                'Случайное изображение с тегами >> /random 1girl gloves'
            ])->join("\n"));
        });

        $bot->onCommand('unsafe', function (Nutgram $bot) {
            $user            = $bot->get('user');
            $user->safe_mode = false;
            $user->save();

            $bot->sendMessage(text: 'Безопасный режим выключен');
        });

        $bot->onCommand('safe', function (Nutgram $bot) {
            $user            = $bot->get('user');
            $user->safe_mode = true;
            $user->save();

            $bot->sendMessage(text: 'Безопасный режим включен');
        });

        $bot->registerCommand(BrowseCommand::class);
        $bot->registerCommand(RandomCommand::class);
        $bot->registerCommand(RandomTagCommand::class);

        $bot->run();
    }
}
