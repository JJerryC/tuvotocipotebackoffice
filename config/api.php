<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Llave API para acceso a la API de Candidatos
    |--------------------------------------------------------------------------
    |
    | Esta clave se utiliza para autenticar todas las peticiones a los
    | endpoints de candidatos que exponen datos sensibles.
    |
    */
    'candidates_api_key' => env('CANDIDATES_API_KEY'),
];
