<?php

namespace Tests\Unit;

use App\Services\PrestamoService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class PrestamoServiceMantenimientoTest extends CIUnitTestCase
{
    public function testPermiteAntesDelLimite(): void
    {
        // Arrange
        $service = new PrestamoService(3);

        // Act
        $resultado = $service->puedeRegistrar(2);

        // Assert
        $this->assertTrue($resultado);
    }

    public function testRechazaAlAlcanzarElLimite(): void
    {
        $service = new PrestamoService(3);

        $this->assertFalse($service->puedeRegistrar(3));
    }

    public function testRechazaPorEncimaDelLimite(): void
    {
        $service = new PrestamoService(3);

        $this->assertFalse($service->puedeRegistrar(4));
    }
}
