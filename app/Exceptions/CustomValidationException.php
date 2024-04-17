<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CustomValidationException extends ValidationException
{
    public function render($request): JsonResponse
    {
        return new JsonResponse([
            'status' => false,
            'errors' => $this->validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
