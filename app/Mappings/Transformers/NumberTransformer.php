<?php

namespace App\Mappings\Transformers;

use Illuminate\Support\Number;
use Illuminate\Support\Arr;
use Brick\Money\Money;

/**
 * Abstract Class NumberTransformer
 *
 * Provides a base implementation for number-based transformations using Laravel's **Number** helper.
 * Concrete subclasses define specific number transformation commands (e.g., percentage, format).
 *
 * ---
 *
 * ## **Core Responsibilities**
 * - Extracts numeric values from input data.
 * - Ensures that monetary values (`Money` objects) are converted to floating-point numbers.
 * - Calls Laravel's `Number::<command>()` dynamically, where `<command>` is defined in child classes.
 *
 * ---
 *
 * ## **Example Usage**
 *
 * ```php
 * $transformer = new NumberPercentTransformer();
 * $result = $transformer->transform(['value' => 0.25]);
 * echo $result['value']; // "25%"
 * ```
 *
 * ---
 *
 * ## **Extending This Class**
 * To create a new number-based transformer, extend this class and define a `$command` property.
 * ```php
 * class NumberCurrencyTransformer extends NumberTransformer
 * {
 *     protected string $command = 'currency';
 * }
 * ```
 */
abstract class NumberTransformer extends BaseTransformer
{
    /**
     * @var string The Laravel `Number` helper method to be invoked.
     */
    protected string $command;

    /**
     * Transform the given number using the specified Laravel `Number` helper method.
     *
     * @param array $data The input data, expected to contain a 'value' key with a numeric value.
     * @return array The transformed value.
     */
    public function transform(array $data): array
    {
        $command = $this->getCommand();
        $args = array_merge($this->options, ['number' => $this->getNumberFromData($data)]);

        return [
            'value' => Number::$command(...$args),
        ];
    }

    /**
     * Retrieve the command that should be executed on the `Number` helper.
     *
     * @return string The transformation command.
     */
    protected function getCommand(): string
    {
        return $this->command;
    }

    /**
     * Extract and normalize the number from input data.
     *
     * If the value is a `Money` object, it is converted to a float.
     *
     * @param array $data Input data, expected to contain 'value'.
     * @return float The extracted numeric value.
     */
    public function getNumberFromData(array $data): float
    {
        $value = Arr::get($data, 'value');

        return $value instanceof Money ? $value->getAmount()->toFloat() : (float) $value;
    }
}
