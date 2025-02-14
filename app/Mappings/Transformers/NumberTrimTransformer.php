<?php

namespace App\Mappings\Transformers;

/**
 * Class NumberTrimTransformer
 *
 * **Removes trailing zeros** after the decimal point from a given number.
 * Uses Laravel's `Number::trim()` helper method.
 *
 * ---
 *
 * ## **Purpose**
 * - Cleans up **formatted numeric values** by eliminating unnecessary trailing zeros.
 * - Ensures that **whole numbers remain integers** (e.g., `"100.00"` → `100`).
 * - Maintains **decimal precision where necessary** (e.g., `"123.45"` stays `123.45`).
 *
 * ---
 *
 * ## **Example Usage**
 *
 * ```php
 * $transformer = new NumberTrimTransformer();
 *
 * $result1 = $transformer->transform(['value' => 123.45000]);
 * echo $result1['value']; // 123.45
 *
 * $result2 = $transformer->transform(['value' => 100.000]);
 * echo $result2['value']; // 100
 *
 * $result3 = $transformer->transform(['value' => 45.600]);
 * echo $result3['value']; // 45.6
 * ```
 *
 * ---
 *
 * ## **Key Features**
 * - ✅ **Automatic trimming** of unnecessary decimal places.
 * - ✅ **No additional options needed**—it works out of the box.
 * - ✅ **Supports both integers and floating-point numbers.**
 * - ✅ **Preserves necessary decimal precision.**
 * - ✅ **Ensures numbers are always returned as their proper numeric type.**
 *
 * ---
 *
 * ## **Use Cases**
 * - **User Interfaces:** Display numbers in a clean, readable format.
 * - **Financial Applications:** Ensure accurate representation of currency values.
 * - **Reports & Invoices:** Remove redundant `.00` decimals in generated documents.
 * - **Scientific Data:** Ensure consistent numerical formatting.
 */
class NumberTrimTransformer extends NumberTransformer
{
    /**
     * @var string The Laravel `Number` helper method used for transformation.
     */
    protected string $command = 'trim';
}
