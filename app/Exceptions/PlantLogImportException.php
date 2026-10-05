<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class PlantLogImportException extends Exception
{
    /** @var array<int, string> */
    protected array $details;

    /** @param array<int, string> $details */
    public function __construct(string $message, array $details = [], int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->details = $details;
    }

    /** @return array<int, string> */
    public function details(): array
    {
        return $this->details;
    }
}
