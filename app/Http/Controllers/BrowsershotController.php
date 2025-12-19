<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class BrowsershotController extends Controller
{
    /**
     * Display test image with text overlay.
     * This route displays a 800x600 image with text overlay.
     */
    public function testImage(Request $request)
    {
        $text = $request->query('text', 'Default Text');

        return view('testimage', [
            'text' => $text,
        ]);
    }

    /**
     * Capture screenshot of the test image.
     * This route generates a PNG screenshot of the /testimage route with the provided text parameter.
     */
    public function screenshot(Request $request)
    {
        $text = $request->query('text', 'test');

        // Read and encode background image as base64
        $backgroundPath = public_path('background.png');
        $backgroundData = base64_encode(file_get_contents($backgroundPath));
        $backgroundUrl = "data:image/png;base64,{$backgroundData}";

        // Escape text to prevent XSS
        $escapedText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Image</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            width: 800px;
            height: 600px;
            overflow: hidden;
            background-image: url('{$backgroundUrl}');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }
        .text-overlay {
            font-size: 48px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            text-align: center;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="text-overlay">
        {$escapedText}
    </div>
</body>
</html>
HTML;

        // Create the screenshot using Browsershot with HTML
        $screenshot = Browsershot::html($html)
            ->setChromePath('/usr/bin/google-chrome')
            ->windowSize(800, 600)
            ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'])
            ->screenshot();

        return response($screenshot)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'inline; filename="screenshot.png"');
    }
}
