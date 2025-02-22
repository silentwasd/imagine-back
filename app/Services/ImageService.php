<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * @throws Exception
     */
    public function normalizeImage(string $filename, int $targetWidth, int $targetHeight)
    {
        $imageInfo = getimagesize($filename);

        $image = match ($mime = $imageInfo['mime']) {
            'image/jpeg' => imagecreatefromjpeg($filename),
            'image/png'  => imagecreatefrompng($filename),
            'image/webp' => imagecreatefromwebp($filename),
            default      => throw new Exception('Unsupported image format: ' . $mime),
        };

        if (!$image)
            throw new Exception('Failed to load image');

        // Создаём новое изображение фиксированного размера (без прозрачности)
        $normalized = imagecreatetruecolor($targetWidth, $targetHeight);

        // Устанавливаем белый фон (чтобы убрать прозрачность)
        $white = imagecolorallocate($normalized, 255, 255, 255);
        imagefill($normalized, 0, 0, $white);

        // Масштабируем изображение
        imagecopyresampled($normalized, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, imagesx($image), imagesy($image));

        // Переводим изображение в черно-белый формат (градации серого)
        imagefilter($normalized, IMG_FILTER_GRAYSCALE);

        // Освобождаем память
        imagedestroy($image);

        return $normalized;
    }

    /**
     * @throws Exception
     */
    public function pHash($filename): string
    {
        $image = $this->normalizeImage($filename, 32, 32); // 32x32 для pHash

        $width = imagesx($image);
        $height = imagesy($image);
        $pixels = [];

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($image, $x, $y);
                $gray = ($rgb >> 16) & 0xFF; // Градации серого: R=G=B

                // Жесткое округление (квантизация)
                $gray = floor($gray / 8) * 8;

                $pixels[] = $gray;
            }
        }

        imagedestroy($image);

        // Среднее значение пикселей
        $average = array_sum($pixels) / count($pixels);

        // Строим pHash: 1, если пиксель больше среднего, 0 — иначе
        $hashBits = '';
        foreach ($pixels as $pixel) {
            $hashBits .= ($pixel >= $average) ? '1' : '0';
        }

        // Конвертируем в хэш (hex)
        $hash = '';
        for ($i = 0; $i < strlen($hashBits); $i += 4) {
            $hash .= dechex(bindec(substr($hashBits, $i, 4)));
        }

        return $hash;
    }
}
