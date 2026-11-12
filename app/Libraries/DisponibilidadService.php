<?php

namespace App\Libraries;

/**
 * Regla de disponibilidad de cupos por bloque y fecha (RF-02).
 */
class DisponibilidadService
{
    /**
     * Indica si quedan cupos en un bloque para una fecha.
     *
     * @param int $ocupados  Solicitudes ya registradas en el bloque/fecha.
     * @param int $capacidad Capacidad maxima configurada para el bloque.
     */
    public function hayCupo(int $ocupados, int $capacidad): bool
    {
        return $ocupados <= $capacidad;
    }
}
