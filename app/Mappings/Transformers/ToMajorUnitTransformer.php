<?php

namespace App\Mappings\Transformers;

use Illuminate\Support\Number;
use Brick\Math\RoundingMode;
use App\Enums\MappingType;
use Brick\Money\Money;

/**
 * Class ToMajorUnitTransformer
 *
 * Converts **monetary values in minor units** (e.g., centavos) into **major units** (e.g., pesos).
 * This ensures precision, proper rounding, and allows optional casting to a specific type.
 *
 * ---
 *
 * ## **How It Works**
 *
 * - If **no `type` option is provided**, it **returns a `Money` object**.
 * - If **a `type` option is provided**, it **casts the value** to the specified type (`float`, `integer`).
 * - Uses **RoundingMode::UP** to ensure that monetary values are rounded up correctly.
 *
 * ---
 *
 * ## **Example Usage**
 *
 * ### **Input (Minor Units)**
 * ```php
 * ['value' => 100000]  // 100,000 centavos (₱1,000.00)
 * ```
 *
 * ### **Output (Major Units)**
 * - Default (returns a `Money` object)
 * ```php
 * ['value' => Money::of(1000.00, 'PHP')]
 * ```
 * - With `type=float`
 * ```php
 * ['value' => 1000.00]
 * ```
 * - With `type=integer`
 * ```php
 * ['value' => 1000]
 * ```
 *
 * ---
 *
 * ## **Integration in Pipelines**
 * ```php
 * $mapping = Mapping::factory()->make([
 *     'transformer' => 'ToMajorUnit?type=float',
 * ]);
 *
 * $pipe = new TransformPipe($mapping);
 * $result = $pipe->handle(['value' => 100000], fn($value) => $value);
 *
 * echo $result['value']; // Output: 1000.00
 * ```
 */
class ToMajorUnitTransformer extends BaseTransformer
{
    private const TYPE_PARAM = 'type';

    protected array $options = [];

    /**
     * Constructor to parse options.
     *
     * @param string|null $option A query-string formatted string (e.g., "type=float").
     */
    public function __construct(?string $option = '')
    {
        parse_str($option, $this->options);
    }

    /**
     * Converts the given value from minor to major units.
     *
     * @param array $data The input data containing the monetary value in minor units.
     *                    - Example: ['value' => 100000] (₱1,000.00)
     *
     * @return array The transformed value in major units.
     *               - Default: Money object.
     *               - With type option: float or integer.
     */
    public function transform(array $data): array
    {
        $money = Money::ofMinor(
            $data['value'],  // The input value in minor units (e.g., centavos)
            Number::defaultCurrency(),  // Retrieve the default application currency
            roundingMode: RoundingMode::UP  // Always round up when necessary
        );

        // If no `type` option is provided, return Money object
        if (!$this->hasOption(self::TYPE_PARAM)) {
            return ['value' => $money];
        }

        // Otherwise, cast to the specified type (float, integer)
        $castFunction = (MappingType::tryFrom($this->getOption(self::TYPE_PARAM)) ?? MappingType::FLOAT)->toType();

        return ['value' => $money->getAmount()->$castFunction()];
    }

    /**
     * Check if a given option exists in the query parameters.
     *
     * @param string $key The option key to check.
     * @return bool True if the option exists, otherwise false.
     */
    protected function hasOption(string $key): bool
    {
        return array_key_exists($key, $this->options);
    }
}
