<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Vite;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Test tidak bergantung pada hasil build aset (npm run build).
        // withoutVite() tidak mencakup direktif @fonts, jadi arahkan ke
        // manifest font yang tidak ada agar direktif itu menghasilkan string kosong.
        $this->withoutVite();
        app(Vite::class)->useFontsManifestFilename('fonts-manifest.test-tanpa-build.json');
    }
}
