<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class GeoDireccionFactory extends Factory
{
    // Define el modelo si lo tienes, o déjalo vacío si usas DB puros
    public function definition()
    {
        return [
            // Faker generará direcciones realistas estilo "Calle 123 # 45-67"
            'calle'         => $this->faker->streetAddress(),
            'numero'        => $this->faker->buildingNumber(),
            'codigo_postal' => $this->faker->postcode(),
            // Asigna un ID de ciudad aleatorio entre 1 y los 1123 municipios
            'id_ciudad'     => $this->faker->numberBetween(1, 1123), 
        ];
    }
}