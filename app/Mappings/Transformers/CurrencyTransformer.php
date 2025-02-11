<?php

namespace App\Mappings\Transformers;

use Brick\Money\Money;
use NumberFormatter;

class CurrencyTransformer extends BaseTransformer
{
    protected array $options = [];

    public function __construct(?string $option = '')
    {
        // Parse the query string into an associative array
        parse_str($option, $this->options);
    }

    public function transform(array $data): array
    {
        $value = $data['value'];

        $formatter = new NumberFormatter('en_PH', NumberFormatter::CURRENCY);

        // Get the currency symbol from the options or default to ₱
        $currencySymbol = $this->getOption('currency_symbol', '₱');
        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, $currencySymbol);

        // Set decimal precision if provided
        $decimalPrecision = (int) $this->getOption('decimal', 2);
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

    /**
     * Get the value of an option, with support for flags (e.g., "read_only").
     *
     * If an option is present without a value (e.g., "read_only"), it returns `true`.
     *
     * @param string $key The option key to retrieve.
     * @param mixed $default The default value if the option is not found.
     * @return mixed
     */
    protected function getOption(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->options)) {
            // If the option has an empty value, treat it as a flag set to `true`
            return $this->options[$key] === '' ? true : $this->options[$key];
        }

        return $default;
    }
}
