<?php

namespace App\Telegram\Commands;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ChatAction;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Input\InputMediaPhoto;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;

class BrowseCommand extends Command
{
    protected string $command = 'browse{page}';

    protected ?string $description = 'Browse gallery.';

    public function handle(Nutgram $bot, ?int $page = 1): void
    {
        if (!$page)
            $page = 1;

        $user = $bot->get('user');

        $images = Image::query()
                       ->when($user->safe_mode, fn($when) => $when->where('is_safe', true))
                       ->orderBy('order_id')
                       ->paginate(perPage: 10, page: $page);

        if ($images->isEmpty()) {
            $bot->sendMessage('Ничего не нашлось!');
            return;
        }

        $bot->sendChatAction(ChatAction::UPLOAD_PHOTO);

        $prevPage = $page - 1;
        $nextPage = $page + 1;

        $messages = [
            ...$images->count() > 1 ? $bot->sendMediaGroup(
                media: $images->map(fn(Image $image, int $index) => InputMediaPhoto::make(
                    media: $image->telegram_file_id ?? InputFile::make(Storage::disk('public')->path($image->path)),
                    caption: $index == $images->count() - 1 ? collect([
                        $prevPage >= 1 ? "Предыдущая страница: /browse$prevPage" : null,
                        $nextPage != $images->lastPage() ? "Следующая страница: /browse$nextPage" : null
                    ])->reject(fn($str) => $str === null)->join("\n") : null,
                    parse_mode: ParseMode::MARKDOWN_LEGACY
                ))->all()
            ) : [$bot->sendPhoto(
                photo: $images[0]->telegram_file_id ?? InputFile::make(Storage::disk('public')->path($images[0]->path))
            )]
        ];

        foreach ($messages as $index => $message) {
            $image = $images[$index];

            if (!$image->telegram_file_id) {
                $image->telegram_file_id = $message->photo[count($message->photo) - 1]->file_id;
                $image->save();
            }
        }
    }
}
