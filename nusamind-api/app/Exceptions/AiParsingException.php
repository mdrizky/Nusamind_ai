<?php

namespace App\Exceptions;

use Exception;

class AiParsingException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message ?: 'Nusamind belum yakin dengan inputmu, coba tulis ulang ya', $code, $previous);
    }

    public function render()
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 422);
    }
}
