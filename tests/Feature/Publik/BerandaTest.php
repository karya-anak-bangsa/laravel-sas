<?php

namespace Tests\Feature\Publik;

use Tests\TestCase;

class BerandaTest extends TestCase
{
    public function test_beranda_diarahkan_ke_halaman_masuk(): void
    {
        $this->get(route('beranda'))
            ->assertRedirect(route('admin.masuk'));
    }
}
