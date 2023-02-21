<?php

declare(strict_types=1);


namespace App\Service;


class RandomNumberGenerator
{
    public function generate(): int
    {
        return rand(1, 1000);
    }
}