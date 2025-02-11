<?php

namespace App\Mappings\Transformers;

use Illuminate\Support\Number;
use Brick\Money\Money;

/**
 * Class NumberSpellTransformer
 *
 * **Purpose:**
 * Converts a numeric value into its spelled-out word representation using Laravel’s `Number::spell()`.
 * Useful for invoices, legal documents, or contracts where amounts need to be written in words.
 * Supports customizable options for thresholds (`after`, `until`) and custom suffixes.
 *
 * ---
 * **Available Options:**
 * - `after`: Specifies a threshold above which numbers are spelled out instead of being shown numerically.
 * - `until`: Specifies a threshold below which numbers are spelled out. Numbers above this will be shown numerically.
 * - **Both `after` and `until` can work together to control specific ranges of spelling and numeric display.**
 *
 * ---
 * **Example Usage:**
 * ```
 * NumberSpellTransformer?after=10&until=5
 * ```
 * - **Numeric Input:** `8`
 * - **Output:** `"eight"` (spelled out because `8 < 10`)
 *
 * ---
 * **Behavior with `after` and `until`:**
 * - **after:** Spells out numbers greater than or equal to the specified threshold.
 * - **until:** Spells out numbers up to the specified threshold. Numbers above this will remain numeric.
 *
 * **Examples from Laravel’s Documentation:**
 * ```php
 * use Illuminate\Support\Number;
 *
 * Number::spell(8, until: 10);  // eight
 * Number::spell(10, until: 10); // 10
 * Number::spell(20, until: 10); // 20
 *
 * Number::spell(11, after: 10); // eleven
 * Number::spell(12, after: 10); // twelve
 * ```
 *
 * ---
 * **Pipeline Integration:**
 * Easily integrate this transformer within transformation pipelines:
 *
 * Example:
 * ```php
 * app(Pipeline::class)
 *     ->send(14399.37)  // Major unit input value
 *     ->through([
 *         new NumberSpellTransformer('after=10&until=5'),
 *     ])
 *     ->thenReturn();  // Result depends on input thresholds
 * ```
 */
class NumberSpellTransformer extends BaseTransformer
{
    protected array $options = [];

    /**
     * Constructor to parse query string options.
     *
     * @param string|null $option A query-string formatted string (e.g., "after=10&until=5").
     */
    public function __construct(?string $option = '')
    {
        // Parse the query string into an associative array
        parse_str($option, $this->options);
    }

    /**
     * Transforms the input value into its spelled-out word representation using `Number::spell()`.
     *
     * @param array $data The input data containing the value.
     * @return array The result with the spelled-out value.
     */
    public function transform(array $data): array
    {
        $value = $data['value'];

        return [
            'value' => Number::spell(
                $value instanceof Money ? $value->getAmount()->toFloat() : $value,
                after: $this->getOption('after'),
                until: $this->getOption('until')
            ),
        ];
    }
}
