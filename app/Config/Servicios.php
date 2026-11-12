<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Politica institucional del modulo de Servicios.
 *
 * SC-01: el maximo de cupos por bloque y fecha se define aqui y puede
 * sobrescribirse desde .env con servicios.maxCuposPorBloque = 4.
 */
class Servicios extends BaseConfig
{
    public int $maxCuposPorBloque = 3;
}
