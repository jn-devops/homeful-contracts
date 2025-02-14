<?php

namespace App\Mappings\Transformers;

/**
 * Class LowerCaseTransformer
 *
 * This transformer converts the given input string to **lowercase** while also **trimming** any leading
 * and trailing whitespace. It ensures that text-based values are consistently formatted in lowercase,
 * which can be useful for **case-insensitive comparisons, normalization, and indexing**.
 *
 * ---
 *
 * ## **Purpose**
 * - **Ensures consistency** by converting all text to lowercase.
 * - **Removes unwanted spaces** by trimming whitespace.
 * - **Useful for search indexing, case-insensitive storage, and comparisons.**
 *
 * ---
 *
 * ## **Example Usage**
 *
 * **Input:**
 * ```php
 * ['value' => '  HeLLo WoRLd  ']
 * ```
 *
 * **Output:**
 * ```php
 * ['value' => 'hello world']
 * ```
 *
 * ---
 *
 * ## **Integration in Pipelines**
 * The `LowerCaseTransformer` can be used inside **Laravel Pipelines** to transform data before further processing.
 *
 * ```php
 * $mapping = Mapping::factory()->make([
 *     'transformer' => 'LowerCase',
 * ]);
 *
 * $pipe = new TransformPipe($mapping);
 * $result = $pipe->handle('  HeLLo WoRLd  ', fn($value) => $value);
 *
 * echo $result['value']; // Output: "hello world"
 * ```
 *
 * ---
 *
 * ## **Best Practices**
 * - Use this transformer when **case consistency is required** (e.g., user input, database storage).
 * - Combine with `UpperCaseTransformer` or `TitleCaseTransformer` for **flexible text formatting.**
 *
 */
class LowerCaseTransformer extends BaseTransformer
{
    /**
     * Transforms the input value to lowercase and trims whitespace.
     *
     * @param array $data The input data containing a string to transform.
     *                    - Example: `['value' => '  HeLLo WoRLd  ']`
     *
     * @return array The transformed value with all characters in lowercase.
     *               - Example output: `['value' => 'hello world']`
     */
    public function transform(array $data): array
    {
        return [
            'value' => strtolower(trim($data['value'])),
        ];
    }
}
