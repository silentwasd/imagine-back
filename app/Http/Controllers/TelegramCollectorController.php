<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;

class TelegramCollectorController extends Controller
{
    private const ALLOWED_CHATS = [
        '-1002021472744' => [86],
        '-1002386932224' => [2, 8]
    ];

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke()
    {
        $bot = new Nutgram(config('services.telegram.collector_token'));
        $bot->setRunningMode(Webhook::class);

        $bot->onPhoto(function (Nutgram $bot) {
            if (!(self::ALLOWED_CHATS[(string)$bot->chatId()] ?? false))
                return;

            if (
                is_array(self::ALLOWED_CHATS[(string)$bot->chatId()]) &&
                !in_array($bot->messageThreadId(), self::ALLOWED_CHATS[(string)$bot->chatId()])
            ) return;

            $photoSizes = $bot->message()->photo;
            $maxPhoto   = $photoSizes[count($photoSizes) - 1];

            try {
                $ext = File::extension($bot->getFile($maxPhoto->file_id)->file_path);

                if (!Storage::disk('public')->directoryExists('telegram'))
                    Storage::disk('public')->makeDirectory('telegram');

                $path = 'telegram/' . $maxPhoto->file_id . '.' . $ext;

                $maxPhoto->download(Storage::disk('public')->path($path));

                Image::create(['path' => $path]);
            } catch (Exception $e) {
                // nothing
            }
        });

        $bot->run();
    }
}
