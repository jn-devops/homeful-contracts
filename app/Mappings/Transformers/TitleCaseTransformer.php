<?php

namespace App\Mappings\Transformers;

use Homeful\Contacts\Enums\Suffix;
use Illuminate\Support\Str;

/**
 * Class TitleCaseTransformer
 *
 * **Purpose:**
 * Converts the input string to title case while preserving the case of suffixes (e.g., `Jr.`, `III`)
 * based on the backed enumeration `Homeful\Contacts\Enums\Suffix`.
 * Also trims any leading or trailing spaces before transformation.
 *
 * ---
 * **Key Behavior:**
 * - Removes leading and trailing whitespace using `trim()`.
 * - Converts each word to title case using Laravel's `Str::title()`.
 * - Ensures that name suffixes (e.g., `Jr.`, `III`) are recognized and retain their correct case.
 *
 * ---
 * **Example Usage:**
 * ```php
 * TitleCaseTransformer
 * ```
 * - **Input:** `"  JOHN DOE III  "`
 * - **Output:** `"John Doe III"`
 *
 * ---
 * **Pipeline Integration:**
 * Easily integrates within transformation pipelines:
 * ```php
 * app(Pipeline::class)
 *     ->send("  JOHN DOE JR.  ")
 *     ->through([
 *         new TitleCaseTransformer()
 *     ])
 *     ->thenReturn();  // Result: "John Doe Jr."
 * ```
 */
class TitleCaseTransformer extends BaseTransformer
{
    /**
     * Transforms the input value by trimming and converting it to title case,
     * while preserving the case of suffixes like Jr., III, etc.
     *
     * @param array $data The input data containing the value.
     * @return array The transformed result.
     */
    public function transform(array $data): array
    {
        $value = trim($data['value']);

        // Split the input into words
        $words = explode(' ', $value);

        // Transform each word to title case except recognized suffixes
        $transformedWords = array_map(function ($word) {
            return $this->isSuffix($word) ? $word : Str::title($word);
        }, $words);

        // Join the transformed words
        $transformedValue = implode(' ', $transformedWords);

        return [
            'value' => $transformedValue,
        ];
    }

    /**
     * Check if the given word is a suffix that should retain its original case.
     *
     * @param string $word
     * @return bool
     */
    protected function isSuffix(string $word): bool
    {
        // Compare against all suffixes defined in the Suffix enum
        return in_array($word, array_column(Suffix::cases(), 'value'), true);
    }
}
