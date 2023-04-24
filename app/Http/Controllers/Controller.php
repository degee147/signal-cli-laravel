<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      title="API Documentation for Signal Bot",
 *      version="1.0.0",
 *      description="API documentation",
 *      @OA\Contact(
 *          email="kenwaribo@gmail.com"
 *      )
 *  ),
 *  @OA\Server(
 *      description="Returns App API",
 *      url="https://localhost:8000/"
 *  ),
 *  @OA\PathItem(
 *      path="/"
 *  )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
