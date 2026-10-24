<?php

namespace App\Http\Controllers;

use App\Services\CaptchaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CaptchaController extends Controller
{
    /**
     * Generate and stream the captcha image.
     */
    public function generate(Request $request, CaptchaService $captchaService): Response
    {
        $imageData = $captchaService->renderImage();

        return response($imageData, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT',
        ]);
    }

    /**
     * Return refresh payload for AJAX consumers.
     */
    public function refresh(Request $request, CaptchaService $captchaService): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'url' => route('captcha.image', ['t' => microtime(true)]),
        ]);
    }
}
