<?php

namespace Tests\Feature;

use Tests\TestCase;

class BrowsershotTest extends TestCase
{
    /**
     * Test that the testimage route returns a successful response.
     */
    public function test_testimage_route_returns_successful_response(): void
    {
        $response = $this->get('/testimage');

        $response->assertStatus(200);
    }

    /**
     * Test that the testimage route displays the default text when no parameter is provided.
     */
    public function test_testimage_displays_default_text(): void
    {
        $response = $this->get('/testimage');

        $response->assertStatus(200);
        $response->assertSee('Default Text');
    }

    /**
     * Test that the testimage route displays custom text when a parameter is provided.
     */
    public function test_testimage_displays_custom_text(): void
    {
        $response = $this->get('/testimage?text=Hello World');

        $response->assertStatus(200);
        $response->assertSee('Hello World');
    }

    /**
     * Test that the testimage route properly escapes HTML in text parameter.
     */
    public function test_testimage_escapes_html_in_text(): void
    {
        $response = $this->get('/testimage?text=<script>alert("xss")</script>');

        $response->assertStatus(200);
        $response->assertDontSee('<script>', false);
        $response->assertSee('&lt;script&gt;', false);
    }

    /**
     * Test that the testimage view has correct dimensions set.
     */
    public function test_testimage_has_correct_dimensions(): void
    {
        $response = $this->get('/testimage?text=test');

        $response->assertStatus(200);
        $response->assertSee('width: 800px');
        $response->assertSee('height: 600px');
    }

    /**
     * Test that the screenshot route returns a successful response.
     */
    public function test_screenshot_route_returns_successful_response(): void
    {
        $response = $this->get('/screenshot?text=test');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    /**
     * Test that the screenshot route returns proper content type header.
     */
    public function test_screenshot_returns_png_content_type(): void
    {
        $response = $this->get('/screenshot?text=sample');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    /**
     * Test that the screenshot route returns valid PNG data.
     */
    public function test_screenshot_returns_valid_png_data(): void
    {
        $response = $this->get('/screenshot?text=test');

        $response->assertStatus(200);
        
        // Check PNG signature (first 8 bytes of a PNG file)
        $content = $response->getContent();
        $this->assertStringStartsWith("\x89PNG\r\n\x1a\n", $content);
    }

    /**
     * Test that the screenshot works with special characters.
     */
    public function test_screenshot_works_with_special_characters(): void
    {
        $response = $this->get('/screenshot?text=' . urlencode('Test & Special!'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }
}
