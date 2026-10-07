<?php

namespace App\Enums;

enum Agama: string
{
    case Islam = 'islam';
    case Katolik = 'katolik';
    case Kristen = 'kristen';
    case Hindu = 'hindu';
    case Buddha = 'buddha';
    case Konghucu = 'konghucu';

    public function label(): string
    {
        return $this->name;
    }
}
