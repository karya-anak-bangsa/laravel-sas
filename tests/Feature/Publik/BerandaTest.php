<?php

namespace Tests\Feature\Publik;

use Tests\TestCase;

class BerandaTest extends TestCase
{
    public function test_beranda_dapat_diakses_tanpa_login(): void
    {
        $this->get(route('beranda'))
            ->assertOk()
            ->assertViewIs('publik.beranda')
            ->assertSee('Yayasan Puspita Bangsa Ciputat')
            ->assertSee('SMK Puspita Bangsa');
    }
}
