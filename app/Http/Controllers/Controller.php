<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Murids API Documentation",
    description: "API documentation for Murids Application"
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "Murids API Server"
)]
abstract class Controller
{
    //
}
