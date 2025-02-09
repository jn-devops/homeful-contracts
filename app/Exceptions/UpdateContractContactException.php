<?php

namespace App\Exceptions;

use Exception;

class UpdateContractContactException extends Exception
{
    /**
     * Construct the exception with a custom message and optional context.
     *
     * @param  string  $contactReferenceCode
     * @param  string  $message
     * @param  int  $code
     * @param  Exception|null  $previous
     */
    public function __construct(
        string $contactReferenceCode,
        string $message = 'Failed to update the contract contact.',
        int $code = 0,
        ?Exception $previous = null
    ) {
        $message = "[Contact Reference: {$contactReferenceCode}] {$message}";
        parent::__construct($message, $code, $previous);
    }
}
