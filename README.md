# Browsershot Test Application

A Laravel application demonstrating the integration of [Spatie Browsershot](https://github.com/spatie/browsershot) for capturing screenshots of web pages. This project showcases both web-based and command-line screenshot generation capabilities.

## About This Project

This application provides a practical example of using Browsershot to generate screenshots programmatically. It includes:

- **Web Routes**: Display and capture screenshots via HTTP endpoints
- **Console Commands**: Generate screenshots from the command line
- **Automated Testing**: Comprehensive test suite for all features

## Prerequisites

Before installing this application, ensure you have the following installed:

- **PHP 8.2 or higher**
- **Composer** (PHP dependency manager)
- **Node.js and npm** (for Puppeteer)
- **Chrome or Chromium browser**
- **SQLite** (for the database)

### Installing Chrome/Chromium

**Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install -y chromium-browser
# OR
sudo apt-get install -y google-chrome-stable
```

**macOS:**
```bash
brew install --cask google-chrome
```

**Verify Installation:**
```bash
which google-chrome chromium-browser chromium
```

## Installation

1. **Clone the repository:**
```bash
git clone https://github.com/ndeblauw/browsershottest.git
cd browsershottest
```

2. **Install dependencies and set up the application:**
```bash
composer setup
```

This command will:
- Install PHP dependencies
- Copy `.env.example` to `.env`
- Generate application key
- Run database migrations
- Install npm dependencies
- Build frontend assets

3. **Create the storage directory for screenshots:**
```bash
mkdir -p storage/app/public
chmod -R 775 storage
```

4. **Optional: Link storage to public directory (for web access):**
```bash
php artisan storage:link
```

## Usage

### Starting the Application

Start the development server:
```bash
php artisan serve
```

Or use the full development environment (includes queue worker, logs, and Vite):
```bash
composer dev
```

The application will be available at `http://localhost:8000`

### Web Route Test

#### 1. Test Image Route (`/testimage`)

Displays a 800x600 pixel page with text overlay on a background image.

**Access:**
```bash
# In browser
http://localhost:8000/testimage

# With custom text
http://localhost:8000/testimage?text=Your+Text+Here
```

**Using curl:**
```bash
curl http://localhost:8000/testimage?text=Hello+World
```

**Parameters:**
- `text` (optional): The text to display. Defaults to "Default Text"

#### 2. Screenshot Route (`/screenshot`)

Generates and returns a PNG screenshot of the test image with the provided text.

**Access:**
```bash
# View in browser
http://localhost:8000/screenshot?text=MyScreenshot

# Save to file using curl
curl http://localhost:8000/screenshot?text=MyText -o screenshot.png
```

**Parameters:**
- `text` (optional): The text to include in the screenshot. Defaults to "test"

**Output:**
- PNG image (800x600 pixels)
- Content-Type: `image/png`

### Console Test

The `screenshot:capture` artisan command captures screenshots from the command line and saves them to the storage directory.

**Usage:**
```bash
php artisan screenshot:capture [text] [--output=filename.png]
```

**Arguments:**
- `text`: The text to display in the screenshot (default: "test")

**Options:**
- `--output`: Output filename (default: "screenshot.png")
  - **Relative path**: Saves to `storage/app/public/[filename]`
  - **Absolute path**: Saves to the specified location

**Examples:**

```bash
# Basic usage (saves to storage/app/public/screenshot.png)
php artisan screenshot:capture "Hello World"

# Save with custom filename in storage
php artisan screenshot:capture "Demo" --output=demo-screenshot.png

# Save to absolute path
php artisan screenshot:capture "Test" --output=/tmp/my-screenshot.png
```

**Storage Location:**

Screenshots saved with relative paths are stored in:
```
storage/app/public/
```

You can access these files:
- **Directly**: Navigate to `storage/app/public/` in your filesystem
- **Via symlink** (if you ran `php artisan storage:link`): `http://localhost:8000/storage/[filename]`

**View saved screenshots:**
```bash
# List all screenshots in storage
ls -lh storage/app/public/

# View a specific screenshot
open storage/app/public/screenshot.png  # macOS
xdg-open storage/app/public/screenshot.png  # Linux
```

## Testing

Run the full test suite:
```bash
composer test
# OR
php artisan test
```

Run specific Browsershot tests:
```bash
php artisan test --filter=BrowsershotTest
```

## How It Works

1. **HTML Generation**: The application generates HTML with text overlay and a base64-encoded background image
2. **Browsershot Processing**: Browsershot uses Puppeteer and Chrome/Chromium to render the HTML
3. **Screenshot Capture**: The rendered page is captured as a PNG image at 800x600 pixels
4. **Response/Storage**: The image is returned directly (web route) or saved to file (command)

### Key Features

- Uses system Chrome/Chromium for rendering
- Base64-encoded background image for consistent rendering
- Configurable dimensions (800x600 pixels)
- HTML escaping for security (prevents XSS attacks)
- Comprehensive test coverage

## Troubleshooting

### "Chrome not found" error

Make sure Chrome/Chromium is installed and in your PATH:
```bash
which google-chrome chromium-browser chromium
```

If installed but not found, update the Chrome path in the code or create a symlink.

### Timeout errors

Browsershot may timeout on slower systems. If this happens, you can increase the timeout in the controller/command.

### Permission errors

Ensure the storage directory is writable:
```bash
chmod -R 775 storage
chown -R $USER:www-data storage  # Linux
```

### Puppeteer not found

If you get errors about Puppeteer, ensure npm dependencies are installed:
```bash
npm install
```

## Security Considerations

- Text input is properly escaped using `htmlspecialchars()` to prevent XSS attacks
- Chrome runs with security flags: `--no-sandbox`, `--disable-setuid-sandbox`
- Output paths are validated to prevent directory traversal attacks
- Screenshot generation happens server-side with controlled HTML

## Additional Documentation

For more detailed technical documentation, see [BROWSERSHOT_USAGE.md](BROWSERSHOT_USAGE.md).

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
