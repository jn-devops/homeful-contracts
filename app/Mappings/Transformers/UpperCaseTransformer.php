<?php

namespace App\Mappings\Transformers;

/**
 * Class UpperCaseTransformer
 *
 * **Purpose:**
 * Converts the input string to uppercase after trimming any leading or trailing whitespace.
 * Useful for normalizing data that needs to be stored or displayed in a consistent format.
 *
 * ---
 * **Key Behavior:**
 * - Removes leading and trailing whitespace using `trim()` before converting the string.
 * - Converts all alphabetic characters in the string to uppercase using `strtoupper()`.
 * - Returns the transformed result as part of an associative array.
 *
 * ---
 * **Example Usage:**
 * ```php
 * UpperCaseTransformer
 * ```
 * - **Input:** `"   john doe   "`
 * - **Output:** `"JOHN DOE"`
 *
 * ---
 * **Pipeline Integration:**
 * Easily integrates within transformation pipelines:
 * ```php
 * app(Pipeline::class)
 *     ->send("    christian santos  ")  // Raw input value
 *     ->through([
 *         new UpperCaseTransformer()
 *     ])
 *     ->thenReturn();  // Result: "CHRISTIAN SANTOS"
 * ```
 */
class UpperCaseTransformer extends BaseTransformer
{
    /**
     * Transforms the input value by trimming and converting it to uppercase.
     *
     * @param array $data The input data containing the value.
     * @return array The transformed result.
     */
    public function transform(array $data): array
    {
        return [
            'value' => strtoupper(trim($data['value'])),
        ];
    }
}
