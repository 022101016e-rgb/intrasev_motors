<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Prueba de humo minima para verificar que el entorno de PHPUnit
 * arranca CodeIgniter y puede resolver las clases de la aplicacion.
 */
final class EntornoTest extends CIUnitTestCase
{
    public function testElEntornoDePruebasEstaActivo(): void
    {
        $this->assertSame('testing', ENVIRONMENT);
    }

    public function testLaAplicacionSeCargaCorrectamente(): void
    {
        $this->assertTrue(class_exists(\App\Controllers\Home::class));
        $this->assertNotNull(service('router'));
    }
}
