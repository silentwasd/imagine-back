<?php

namespace App\Listeners;

use App\Events\ImageCreated;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MakePreview
{
    public function __construct()
    {
    }

    /**
     * @throws Exception
     */
    public function handle(ImageCreated $event): void
    {
        $imageFile = Storage::disk('public')->path($event->image->path);

        $imageInfo = getimagesize($imageFile);

        $gdImage = match ($mime = $imageInfo['mime']) {
            'image/jpeg' => imagecreatefromjpeg($imageFile),
            'image/png'  => imagecreatefrompng($imageFile),
            'image/webp' => imagecreatefromwebp($imageFile),
            default      => throw new Exception('Unsupported image format: ' . $mime),
        };

        // Новые размеры холста
        $canvasWidth  = 450;
        $canvasHeight = 450;

        // Исходные размеры
        $origWidth  = imagesx($gdImage);
        $origHeight = imagesy($gdImage);

        // Вычисляем новый размер, чтобы картинка полностью заполнила 450x450
        $ratio     = max($canvasWidth / $origWidth, $canvasHeight / $origHeight);
        $newWidth  = (int)($origWidth * $ratio);
        $newHeight = (int)($origHeight * $ratio);

        // Вычисляем смещение для центрирования
        $offsetX = (int)(($canvasWidth - $newWidth) / 2);
        $offsetY = (int)(($canvasHeight - $newHeight) / 2);

        // Создаем новый холст с прозрачным фоном
        $resizedImage = imagecreatetruecolor($canvasWidth, $canvasHeight);
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127); // Полностью прозрачный белый
        imagefilledrectangle($resizedImage, 0, 0, $canvasWidth, $canvasHeight, $transparent);

        // Копируем и масштабируем изображение на холст
        imagecopyresampled(
            $resizedImage, $gdImage,
            $offsetX, $offsetY, 0, 0,
            $newWidth, $newHeight,
            $origWidth, $origHeight
        );

        $path = 'preview/' . Str::random(40) . '.webp';
        Storage::disk('public')->makeDirectory('preview');

        imagewebp(
            $resizedImage,
            Storage::disk('public')->path($path),
            80
        );

        $event->image->preview_path = $path;
        $event->image->save();

        imagedestroy($gdImage);
        imagedestroy($resizedImage);
    }
}
