<?php

namespace Tests\Unit;

use App\Libraries\DisponibilidadService;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * SC-01: politica institucional de 4 cupos por bloque y fecha.
 *
 * La prueba valida la regla con la politica nueva. La lectura del valor
 * desde Config\Servicios y el override de .env se verifica aparte con
 * `php spark config:check Servicios`.
 */
final class PoliticaCuposSc01Test extends CIUnitTestCase
{
    private DisponibilidadService $disponibilidad;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disponibilidad = new DisponibilidadService();
    }

    #[DataProvider('casosPoliticaCuatro')]
    public function testLaPoliticaDeCuatroCupos(int $ocupados, bool $esperado): void
    {
        $this->assertSame(
            $esperado,
            $this->disponibilidad->hayCupo($ocupados, 4)
        );
    }

    public static function casosPoliticaCuatro(): array
    {
        return [
            'sin ocupados'              => [0, true],
            'un cupo ocupado'           => [1, true],
            'dos cupos ocupados'        => [2, true],
            'tres cupos ocupados'       => [3, true],
            'cuatro cupos: lleno'       => [4, false],
            'cinco cupos: ya excedido'  => [5, false],
        ];
    }
}
