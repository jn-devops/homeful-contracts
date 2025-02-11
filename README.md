## About Homeful Contracts

## Usage
`ssh -f -N -L 42170:localhost:3306 forge@contacts.homeful.ph`

# Transformer Usage Guide

This guide provides an overview of how to define and use transformers within the application. Transformers process data values before saving or using them, allowing various types of transformation, formatting, and value manipulation.

## Defining a Transformer

Transformers should be located within the `App\Mappings\Transformers` namespace. They need to extend the custom `AbstractTransformer` class, which provides helper methods like `getOption()` for parsing transformer options.

### Example Transformer: `CurrencyTransformer`

This transformer formats a numeric value into a currency string with support for specifying currency symbols and precision using options.

```php
namespace App\Mappings\Transformers;

use App\Mappings\Transformers\AbstractTransformer;
use NumberFormatter;

class CurrencyTransformer extends AbstractTransformer
{
    public function transform(array $data): array
    {
        $value = $data['value'];
        
        $currencySymbol = $this->getOption('currency_symbol', '₱');
        $decimalPrecision = (int) $this->getOption('decimal', 2);

        $formatter = new NumberFormatter('en_PH', NumberFormatter::CURRENCY);
        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, $currencySymbol);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimalPrecision);

        $formattedValue = $formatter->formatCurrency($value, 'PHP');

        // Normalize non-breaking spaces and return the result
        return [
            'value' => preg_replace('/\p{Zs}+/u', ' ', $formattedValue),
        ];
    }
}
```

## Security Vulnerabilities

If you discover a security vulnerability within Homeful Contacts, please send an e-mail to Anaïs Santos via [devops@joy-nostalg.com](mailto:devops@joy-nostalg.com). All security vulnerabilities will be promptly addressed.

## Credits

- [Anaïs Santos](https://github.com/anais-enclavewrx)
- [All Contributors](../../contributors)
