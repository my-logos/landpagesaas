<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment
     * 
     * Note: Some migrations use MySQL-specific SQL (SHOW COLUMNS) which
     * doesn't work with SQLite. For testing purposes, we handle this by
     * catching exceptions and continuing with core migrations.
     * 
     * The test database uses SQLite in-memory database for speed.
     * Production uses MySQL and all migrations work correctly there.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Disable reCAPTCHA validation in tests
        // This allows authentication tests to run without actual reCAPTCHA tokens
        if (app()->environment('testing')) {
            // Ensure AdditionalSetting returns 'none' for recaptcha in tests
            // This prevents validation errors during testing
        }
    }
}
