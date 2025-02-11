<?php

namespace App\Mappings\Transformers;

use Brick\Money\Money;
use NumberFormatter;

/**
 * Class CurrencyTransformer
 *
 * **Purpose:**
 * Converts a numeric value into a properly formatted currency string.
 * The input value **must be in major units** (e.g., pesos rather than centavos) to maintain accuracy and clarity in currency formatting.
 *
 * **Why Major Units?:**
 * - When working with currency formats, it's best practice to use major units (e.g., PHP 1,000.00 rather than 100,000 centavos).
 * - Major units ensure that when the formatter applies decimal precision, rounding, and currency symbols, the result is accurate without extra conversions.
 * - Avoids unintended errors when handling large amounts or formatting differences between currencies with different decimal places.
 *
 * **How It Works:**
 * - The transformer formats the input value as currency using PHP's `NumberFormatter`.
 * - It dynamically supports custom currency symbols and decimal precision using options provided during configuration.
 *
 * **Options:**
 * - `symbol`: Customize the currency symbol (default: ₱).
 * - `decimals`: Set the number of decimal places (default: 2).
 *
 * Example configuration as part of a transformer string:
 * ```php
 * CurrencyTransformer?symbol=PHP&decimals=2
 * ```
 *
 * **Integration in Pipelines:**
 * This transformer can be used as part of a pipeline where the value flows through several transformation steps before casting and storage.
 *
 * **Example Usage in Pipeline:**
 * ```php
 * app(Pipeline::class)
 *     ->send(1000)  // Major unit input value
 *     ->through([
 *         new CurrencyTransformer('symbol=₱&decimals=2'),
 *     ])
 *     ->thenReturn();  // Result: ₱1,000.00
 * ```
 */
class CurrencyTransformer extends BaseTransformer
{
    const SYMBOL_PARAM = 'symbol';
    const DECIMAL_PARAM = 'decimals';

    protected array $options = [];

    /**
     * Constructor to parse query string options.
     *
     * @param string|null $option A query-string format of options (e.g., "symbol=₱&decimals=2").
     */
    public function __construct(?string $option = '')
    {
        // Parse the query string into an associative array
        parse_str($option, $this->options);
    }

    /**
     * Transforms the input value into a properly formatted currency string.
     *
     * @param array $data The input data containing the value.
     * @return array The formatted currency value.
     */
    public function transform(array $data): array
    {
        $value = $data['value'];

        $formatter = new NumberFormatter('en_PH', NumberFormatter::CURRENCY);

        // Get the currency symbol from the options or default to ₱
        $currencySymbol = $this->getOption(self::SYMBOL_PARAM, '₱');
        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, $currencySymbol);

        // Set decimal precision if provided
        $decimalPrecision = (int) $this->getOption(self::DECIMAL_PARAM, 2);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $decimalPrecision);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimalPrecision);

        $formattedValue = $formatter->formatCurrency(
            $value instanceof Money ? $value->getAmount()->toFloat() : $value,
            'PHP'
        );

        // Normalize any non-breaking spaces
        $formattedValue = preg_replace('/\p{Zs}+/u', ' ', $formattedValue);

        return [
            'value' => $formattedValue,
        ];
    }
}
