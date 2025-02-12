<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception: MissingJoinTransformerException
 *
 * This exception is thrown when a mapping contains multiple paths
 * but does not include the `JoinTransformer`.
 *
 * **Why This Exception?**
 * - Ensures that mappings using multiple comma-separated paths
 *   are properly transformed into a single value.
 * - Improves debugging and maintains consistency in the data pipeline.
 *
 * **Example Scenario**
 * ```php
 * $mapping = Mapping::factory()->make([
 *     'path' => 'first_name,last_name',
 *     'transformer' => 'UpperCase' // ❌ Missing JoinTransformer
 * ]);
 *
 * throw new MissingJoinTransformerException($mapping->code);
 * ```
 *
 * **Expected Exception Message**
 * ```
 * Mapping 'first_name,last_name' contains multiple paths but does not include JoinTransformer.
 * ```
 */
class MissingJoinTransformerException extends Exception
{
    /**
     * Construct the exception with a formatted message.
     *
     * @param string $mappingCode The mapping code that triggered the exception.
     */
    public function __construct(string $mappingCode)
    {
        $message = "Mapping '{$mappingCode}' contains multiple paths but does not include JoinTransformer.";
        parent::__construct($message);
    }
}
