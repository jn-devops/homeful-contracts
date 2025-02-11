<?php

namespace App\Mappings\Transformers;

use Illuminate\Support\Number;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

/**
 * Class ToMajorUnitTransformer
 *
 * This transformer is responsible for converting monetary values expressed in **minor units**
 * (e.g., centavos) into **major units** (e.g., pesos) using the precision and rounding capabilities
 * of the **Brick\Money** library. It is primarily used for ensuring financial data consistency and
 * avoiding floating-point errors when working with monetary amounts.
 *
 * ---
 *
 * ## Purpose of Minor Unit Conversion
 *
 * **Why Use Minor Units Instead of Major Units?**
 * 1. **Precision Control:**
 *    Storing monetary values as integers in minor units (e.g., 10000 for ₱100.00) prevents rounding
 *    errors and ensures precise arithmetic operations without floating-point inaccuracies.
 *
 * 2. **Best Practices in Financial Systems:**
 *    Financial systems typically store values in minor units to maintain consistency, support
 *    calculations without precision loss, and avoid discrepancies when integrating with external
 *    systems or performing bulk arithmetic operations.
 *
 * 3. **Compatibility with APIs and External Systems:**
 *    Many payment gateways, banks, and accounting systems expect monetary values in minor units,
 *    making this conversion necessary when handling such data.
 *
 * ---
 *
 * ## How the Transformer Works
 *
 * 1. **Input:**
 *    The input to this transformer is a monetary value provided in **minor units**.
 *    Example input:
 *    ```php
 *    ['value' => 100000]  // Represents ₱1,000.00 (100,000 centavos)
 *    ```
 *
 * 2. **Conversion to Major Units:**
 *    The transformer divides the minor unit value by the correct factor based on the currency (e.g.,
 *    100 for PHP). The resulting value is converted into major units using **Brick\Money::ofMinor()**.
 *
 * 3. **Rounding Mode:**
 *    - The transformer uses **RoundingMode::UP**, ensuring that fractional values are always rounded up.
 *      Example:
 *      - 999.999 pesos will be rounded to 1,000.00 pesos.
 *
 * 4. **Output:**
 *    The output is a **Money** object representing the value in major units, which can be easily formatted.
 *
 * ---
 *
 * ## Example Usage
 *
 * ### Input (Minor Units)
 * ```php
 * ['value' => 100000]  // 100,000 centavos representing ₱1,000.00
 * ```
 *
 * ### Output (Major Units)
 * ```php
 * ['value' => Money::of(1000.00, 'PHP')]
 * ```
 *
 * ---
 *
 * ## Best Practices
 * - **Store monetary values in minor units:**
 *   This avoids floating-point errors and is a standard in financial applications.
 *
 * - **Apply consistent rounding strategies:**
 *   Using **RoundingMode::UP** ensures safe rounding when dealing with fractions, which is particularly
 *   useful for invoicing, tax calculations, or rounding up values.
 *
 * - **Centralize currency conversions:**
 *   This transformer can be reused throughout the application to consistently process and display
 *   monetary values, reducing redundancy and error-prone custom implementations.
 *
 * ---
 *
 * ## Integration in Pipelines
 * This transformer integrates seamlessly into **Laravel pipelines**, enabling automated transformation
 * during mapping processes. For example:
 *
 * ```php
 * $mapping = Mapping::factory()->make([
 *     'transformer' => 'ToMajorUnit',
 * ]);
 *
 * $pipe = new TransformPipe($mapping);
 * $result = $pipe->handle('100000', fn($value) => $value);
 *
 * echo $result['value'];  // Output: ₱1,000.00
 * ```
 */
class ToMajorUnitTransformer extends BaseTransformer
{
    /**
     * Transform the given value from minor units to major units.
     *
     * This method converts an integer representing minor units (e.g., centavos) into major units
     * (e.g., pesos) using the default currency and rounding mode.
     *
     * @param array $data The input data containing the monetary value in minor units.
     *                    - Example: ['value' => 100000] means 1000.00 pesos.
     *
     * @return array The transformed value in major units.
     *               - Example output: ['value' => Money::of(1000.00, 'PHP')]
     *
     * @throws \Brick\Math\Exception\MathException If the conversion encounters precision issues.
     */
    public function transform(array $data): array
    {
        return [
            'value' => Money::ofMinor(
                $data['value'],  // The input value in minor units (e.g., centavos)
                Number::defaultCurrency(),  // Retrieve the default application currency
                roundingMode: RoundingMode::UP  // Always round up when necessary
            ),
        ];
    }
}
