<?php

namespace App\Mappings\Transformers;

use Illuminate\Support\Str;

class JoinTransformer extends BaseTransformer
{
    /**
     * Transforms a JSON-encoded associative array into a space-separated string.
     *
     * **Behavior:**
     * - If the input value is a valid JSON object, it extracts and joins values into a single string.
     * - If the input is not JSON, it simply returns the trimmed original value.
     *
     * **Example Usage:**
     * ```php
     * $transformer = new JoinTransformer();
     * $result = $transformer->transform(['value' => '{"first_name":"Anais","last_name":"Santos"}']);
     * echo $result['value']; // Output: "Anais Santos"
     * ```
     *
     * **Example Mapping (Multi-Path Extraction)**
     * ```php
     * $mapping = Mapping::factory()->make([
     *     'path' => 'first_name,last_name',
     *     'transformer' => 'Join'
     * ]);
     * ```
     * **Example Input:**
     * ```json
     * {"first_name": "Anais", "last_name": "Santos"}
     * ```
     * **Transformed Output:**
     * ```text
     * "Anais Santos"
     * ```
     *
     * **Failsafe Behavior:**
     * - If the JSON contains empty values, they are omitted.
     * - If the value is not a JSON string, it is returned as-is.
     *
     * @param array $data The input data containing the key 'value'.
     * @return array The transformed output.
     */
    public function transform(array $data): array
    {
        $value = trim($data['value']);

        // Check if the value is JSON
        if (validateJson($value)) {
            // Decode JSON to an associative array and join values with spaces
            $transformedValue = implode(' ', array_filter(json_decode($value, true)));

            return [
                'value' => $transformedValue,
            ];
        }

        // If not JSON, return the original value
        return [
            'value' => $value,
        ];
    }
}
