<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The branded error pages in resources/views/errors render on their own (no session, no database)
 * and are used for real errors.
 */
class ErrorPagesTest extends TestCase
{
    public function test_unknown_url_shows_the_library_404_page(): void
    {
        $this->get("/this/page/does-not-exist")
            ->assertNotFound()
            ->assertSee("Sorry, page not found")
            ->assertSee("Go to Login")
            ->assertSee("/assets/css/app.min.css", false); // absolute paths, so deep URLs still get styled
    }

    public function test_every_error_page_renders(): void
    {
        $titles = [
            "401" => "Please sign in",
            "403" => "have access to this page",
            "404" => "Sorry, page not found",
            "419" => "This page has expired",
            "429" => "Too many attempts",
            "500" => "Something went wrong",
            "503" => "The library is being updated",
            "4xx" => "be completed",
            "5xx" => "Something went wrong",
        ];
        foreach ($titles as $code => $title) {
            $html = view("errors.$code")->render();
            $this->assertStringContainsString($title, $html, "errors.$code");
            $this->assertStringContainsString("Ladybird Library System", $html, "errors.$code");
        }
    }
}
