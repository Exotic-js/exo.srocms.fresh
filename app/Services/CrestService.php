<?php

namespace App\Services;

class CrestService
{
    public static function generateGuildCrest(string $bin, int $width = 16, int $height = 16) {
        $palette = config('palette');

        $img = imagecreatetruecolor($width, $height);
        imagesavealpha($img, true);
        imagealphablending($img, true);

        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $transparent);

        $colors = [];
        foreach ($palette as $index => $hex) {
            [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');
            $colors[$index] = imagecolorallocate($img, $r, $g, $b);
        }

        $bytes = str_split(substr($bin, 2), 2);

        foreach ($bytes as $i => $byte) {
            $y = intdiv($i, $width);
            if ($y >= $height) {
                break;
            }

            $x = $i % $width;
            $colorIndex = hexdec($byte);

            imagesetpixel(
                $img,
                $x,
                $y,
                $colors[$colorIndex] ?? $transparent
            );
        }

        imageflip($img, IMG_FLIP_VERTICAL);

        return $img;
    }
}
