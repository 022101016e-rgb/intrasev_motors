<?php

namespace App\Services;

use InvalidArgumentException;

final class PrestamoService
{
    public function __construct(private int $maximo = 3)
    {
        if ($maximo < 1) {
            throw new InvalidArgumentException('Limite invalido');
        }
    }

    public function limite(): int
    {
        return $this->maximo;
    }

    public function puedeRegistrar(int $actuales): bool
    {
        if ($actuales < 0) {
            throw new InvalidArgumentException('Cantidad negativa');
        }

        // DEFECTO DOCENTE INC-01: se analizara en A4.
        return $actuales <= $this->maximo;
    }
}
