<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Dilempar saat data yang akan dihapus masih dirujuk data lain.
 *
 * Dirender sebagai redirect kembali dengan pesan `galat` (bootstrap/app.php).
 */
class DataMasihDipakai extends RuntimeException
{
    //
}
