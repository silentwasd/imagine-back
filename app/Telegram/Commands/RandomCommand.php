<?php

namespace App\Telegram\Commands;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ChatAction;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;

class RandomCommand extends Command
{
    protected string $command = 'random';

    protected ?string $description = 'Get random image.';

    public function handle(Nutgram $bot, ?string $tag = null): void
    {
        $user = $bot->get('user');

        $image = Image::query()
                      ->when($tag, fn($when) => $when
                          ->whereHas('tags', fn($has) => $has->whereIn('name', explode(' ', $tag)), '=', count(explode(' ', $tag)))
                      )
                      ->when($user->safe_mode, fn($when) => $when->where('is_safe', true))
                      ->inRandomOrder()
                      ->first();

        if (!$image) {
            $bot->sendMessage('Ничего не нашлось!');
            return;
        }

        $bot->sendChatAction(ChatAction::UPLOAD_PHOTO);

        $path = Storage::disk('public')->path($image->path);

        $message = $bot->sendPhoto(
            photo: $image->telegram_file_id ?? InputFile::make($path),
            caption: $image->tags()->orderByDesc('frequency')->get()->map(fn($tag) => '#' . $tag->name)->join(' ')
        );

        if (!$image->telegram_file_id) {
            $image->telegram_file_id = $message->photo[count($message->photo) - 1]->file_id;
            $image->save();
        }
    }
}
