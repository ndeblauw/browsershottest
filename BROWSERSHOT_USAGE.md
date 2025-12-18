# Browsershot Implementation Guide

This application demonstrates the integration of [Spatie Browsershot](https://github.com/spatie/browsershot) for capturing screenshots of web pages.

## Features

### 1. Test Image Route (`/testimage`)
Displays a 800x600 pixel page with text overlay on a background image.

**Usage:**
- Visit: `http://localhost:8000/testimage`
- With parameter: `http://localhost:8000/testimage?text=Your+Text+Here`

**Parameters:**
- `text` (optional): The text to display. Defaults to "Default Text"

**Example:**
```bash
curl http://localhost:8000/testimage?text=Hello+World
```

### 2. Screenshot Route (`/screenshot`)
Generates a PNG screenshot of the test image with the provided text parameter.

**Usage:**
- Visit: `http://localhost:8000/screenshot`
- With parameter: `http://localhost:8000/screenshot?text=test`

**Parameters:**
- `text` (optional): The text to include in the screenshot. Defaults to "test"

**Example:**
```bash
# Save screenshot to file
curl http://localhost:8000/screenshot?text=MyText -o screenshot.png

# View in browser
open http://localhost:8000/screenshot?text=Example
```

**Output:**
- PNG image (800x600 pixels)
- Content-Type: `image/png`

### 3. Artisan Command (`screenshot:capture`)
Captures a screenshot from the command line and saves it to a file.

**Usage:**
```bash
php artisan screenshot:capture [text] [--output=filename.png]
```

**Arguments:**
- `text`: The text to display in the screenshot (default: "test")

**Options:**
- `--output`: Output filename (default: "screenshot.png")
  - If relative path is provided, saves to `storage/app/public/`
  - If absolute path is provided, saves to that location

**Examples:**
```bash
# Basic usage (saves to storage/app/public/screenshot.png)
php artisan screenshot:capture "Hello World"

# Save to specific location
php artisan screenshot:capture "Test" --output=/tmp/my-screenshot.png

# Save with custom filename in storage
php artisan screenshot:capture "Demo" --output=demo-screenshot.png
```

## Technical Implementation

### How It Works
1. **HTML Generation**: The application generates HTML with the text overlay and background image (embedded as base64)
2. **Browsershot Processing**: Browsershot uses Puppeteer and Chrome/Chromium to render the HTML
3. **Screenshot Capture**: The rendered page is captured as a PNG image at 800x600 pixels
4. **Response**: The image is returned directly (web route) or saved to file (command)

### Key Features
- Uses system Chrome/Chromium for rendering
- Base64-encoded background image for consistent rendering
- Configurable dimensions (800x600 pixels)
- HTML escaping for security
- Comprehensive test coverage

### Requirements
- PHP 8.2+
- Chrome or Chromium browser
- Node.js and npm (for Puppeteer)
- spatie/browsershot package

## Testing

Run the test suite:
```bash
php artisan test
```

Run specific browsershot tests:
```bash
php artisan test --filter=BrowsershotTest
```

## Security Considerations
- Text input is properly escaped to prevent XSS
- Chrome runs with security flags: `--no-sandbox`, `--disable-setuid-sandbox`
- Screenshot generation happens server-side with controlled HTML

## Example Outputs

### Test Image Route
![Test Image Example](https://github.com/user-attachments/assets/f945fe60-3a3b-41f3-8ad1-93281043ade3)

### Screenshot Route
Generates a PNG file identical to the test image route output.

## Troubleshooting

### "Chrome not found" error
Make sure Chrome/Chromium is installed:
```bash
which google-chrome chromium-browser chromium
```

### Timeout errors
Increase the timeout in the controller if needed (currently uses default timeout).

### Permission errors
Ensure the `storage/app/public` directory is writable:
```bash
chmod -R 775 storage/app/public
```
