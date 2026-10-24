<?php

namespace App\Services;

class CaptchaService
{
    protected string $sessionKey = 'captcha_code';

    /**
     * Generate a new captcha code and store in session.
     */
    public function generateCode(int $length = 5): string
    {
        // Avoid ambiguous characters like 0, O, 1, I, L
        $characters = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $code = '';
        $maxIndex = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, $maxIndex)];
        }

        session([$this->sessionKey => $code]);

        return $code;
    }

    /**
     * Get the stored captcha code from session.
     */
    public function getStoredCode(): ?string
    {
        return session($this->sessionKey);
    }

    /**
     * Clear the captcha code from session.
     */
    public function clear(): void
    {
        session()->forget($this->sessionKey);
    }

    /**
     * Validate the given code against the session code.
     */
    public function validate(?string $input): bool
    {
        $stored = $this->getStoredCode();
        if (empty($stored) || empty($input)) {
            return false;
        }

        return strcasecmp($stored, trim($input)) === 0;
    }

    /**
     * Render the captcha PNG image.
     */
    public function renderImage(?string $code = null, int $width = 160, int $height = 46): string
    {
        if ($code === null) {
            $code = $this->generateCode();
        }

        $image = imagecreatetruecolor($width, $height);

        // Anti-aliasing
        if (function_exists('imageantialias')) {
            imageantialias($image, true);
        }

        // Background color (soft light gray/blue)
        $bgColor = imagecolorallocate($image, 245, 247, 250);
        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

        // Subtle background dots
        for ($i = 0; $i < 60; $i++) {
            $dotColor = imagecolorallocate($image, random_int(200, 230), random_int(200, 230), random_int(210, 240));
            imagesetpixel($image, random_int(0, $width), random_int(0, $height), $dotColor);
        }

        // Subtle background wavy/distort lines
        for ($i = 0; $i < 3; $i++) {
            $lineColor = imagecolorallocate($image, random_int(180, 220), random_int(180, 220), random_int(190, 230));
            imagesetthickness($image, random_int(1, 2));
            imageline(
                $image,
                random_int(0, 20),
                random_int(5, $height - 5),
                random_int($width - 20, $width),
                random_int(5, $height - 5),
                $lineColor
            );
        }

        // Color palette for characters (readable, rich dark colors)
        $charColors = [
            imagecolorallocate($image, 30, 58, 138),   // Navy blue
            imagecolorallocate($image, 159, 18, 57),  // Crimson / Wine
            imagecolorallocate($image, 15, 118, 110), // Teal
            imagecolorallocate($image, 107, 33, 168), // Purple
            imagecolorallocate($image, 55, 48, 163),  // Indigo
            imagecolorallocate($image, 22, 101, 52),  // Deep green
            imagecolorallocate($image, 51, 65, 85),   // Slate
        ];

        $fontPath = resource_path('fonts/captcha.ttf');
        $hasTtf = file_exists($fontPath) && function_exists('imagettftext');

        $length = strlen($code);
        $charSpacing = ($width - 30) / max(1, $length);

        for ($i = 0; $i < $length; $i++) {
            $char = $code[$i];
            $color = $charColors[array_rand($charColors)];

            if ($hasTtf) {
                $fontSize = random_int(20, 24);
                $angle = random_int(-15, 15);
                $x = (int)(15 + ($i * $charSpacing) + random_int(-2, 2));
                $y = (int)(($height / 2) + ($fontSize / 2) + random_int(-2, 2));

                // Draw slight shadow for readability
                $shadowColor = imagecolorallocatealpha($image, 180, 190, 205, 70);
                imagettftext($image, $fontSize, $angle, $x + 1, $y + 1, $shadowColor, $fontPath, $char);

                // Draw character
                imagettftext($image, $fontSize, $angle, $x, $y, $color, $fontPath, $char);
            } else {
                // Fallback using built-in GD font
                $x = (int)(15 + ($i * $charSpacing) + random_int(-2, 2));
                $y = (int)(($height / 2) - 8 + random_int(-2, 2));
                imagestring($image, 5, $x, $y, $char, $color);
            }
        }

        // Add 1 gentle foreground scratch line
        $fgLineColor = imagecolorallocatealpha($image, random_int(120, 180), random_int(120, 180), random_int(140, 200), 80);
        imagesetthickness($image, 1);
        imageline(
            $image,
            random_int(0, 15),
            random_int(10, $height - 10),
            random_int($width - 15, $width),
            random_int(10, $height - 10),
            $fgLineColor
        );

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        if (PHP_VERSION_ID < 80500) {
            imagedestroy($image);
        }

        return $imageData;
    }
}
