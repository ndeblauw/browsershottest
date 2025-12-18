<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Browsershot\Browsershot;

class CaptureScreenshotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'screenshot:capture {text=test} {--output=screenshot.png}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Capture a screenshot of the testimage route with the given text';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $text = $this->argument('text');
        $outputPath = $this->option('output');
        
        // Ensure output path is absolute
        if (!str_starts_with($outputPath, '/')) {
            $outputPath = storage_path('app/public/' . $outputPath);
        }
        
        // Ensure directory exists
        $directory = dirname($outputPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $this->info("Capturing screenshot with text: {$text}");
        
        // Read and encode background image as base64
        $backgroundPath = public_path('background.png');
        $backgroundData = base64_encode(file_get_contents($backgroundPath));
        $backgroundUrl = "data:image/png;base64,{$backgroundData}";
        
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
        {$text}
    </div>
</body>
</html>
HTML;
        
        try {
            // Create the screenshot using Browsershot with HTML
            Browsershot::html($html)
                ->setChromePath('/usr/bin/google-chrome')
                ->windowSize(800, 600)
                ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'])
                ->save($outputPath);
            
            $this->info("✓ Screenshot saved to: {$outputPath}");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to capture screenshot: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
