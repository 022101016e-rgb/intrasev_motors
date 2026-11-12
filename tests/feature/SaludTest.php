<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Prueba de humo del endpoint /salud (SM-01).
 * No depende de la base de datos.
 */
final class SaludTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testElEndpointSaludRespondeOk(): void
    {
        $resultado = $this->get('salud');

        $resultado->assertStatus(200);
        $resultado->assertJSONFragment(['estado' => 'ok']);
    }
}
