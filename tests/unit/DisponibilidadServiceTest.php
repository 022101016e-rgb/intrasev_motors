<?php

namespace Tests\Unit;

use App\Libraries\DisponibilidadService;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Pruebas de la regla de cupos por bloque/fecha (RF-02).
 *
 * Capacidad de referencia: 3 cupos por bloque y fecha.
 */
final class DisponibilidadServiceTest extends CIUnitTestCase
{
    private DisponibilidadService $disponibilidad;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disponibilidad = new DisponibilidadService();
    }

    #[DataProvider('casosDeCupo')]
    public function testLaReglaDeCupoRespetaLaCapacidad(
        int $ocupados,
        int $capacidad,
        bool $esperado
    ): void {
        $this->assertSame(
            $esperado,
            $this->disponibilidad->hayCupo($ocupados, $capacidad)
        );
    }

    public static function casosDeCupo(): array
    {
        return [
            'bloque vacio'                  => [0, 3, true],
            'un cupo ocupado'               => [1, 3, true],
            'dos cupos ocupados'            => [2, 3, true],
            'tres cupos ocupados: lleno'    => [3, 3, false],
            'cuatro cupos: ya excedido'     => [4, 3, false],
        ];
    }
}
