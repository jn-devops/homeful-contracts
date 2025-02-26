<?php

namespace App\Mappings\Transformers;

/**
 * Class ConcatTransformer
 *
 * This transformer appends **before** and **after** text to a given value while ensuring proper spacing.
 *
 * **Usage Examples:**
 * - Input: `"John"` → Transformer: `before=Hello&after=!"` → Output: `"Hello John!"`
 * - Input: `"Apple"` → Transformer: `before=Item - ` → Output: `"Item - Apple"`
 * - Input: `"123"` → Transformer: `after= USD` → Output: `"123 USD"`
 */
class ConcatTransformer extends BaseTransformer
{
    /** Query parameter for the prefix text */
    const BEFORE_PARAM = 'before';

    /** Query parameter for the suffix text */
    const AFTER_PARAM = 'after';

    /** Holds parsed query string options */
    protected array $options = [];

    /**
     * Constructor to parse transformer options from a query string.
     *
     * @param string|null $option Query string options (e.g., `"before=Hello&after=!"`).
     */
    public function __construct(?string $option = '')
    {
        // Convert query string into an associative array (e.g., ['before' => 'Hello', 'after' => '!'])
        parse_str($option, $this->options);
    }

    /**
     * Applies before/after concatenation transformation.
     *
     * **Steps:**
     * 1. Retrieves **before** and **after** options.
     * 2. Ensures correct trimming and spacing.
     * 3. Returns the formatted result.
     *
     * @param array $data Input data containing `value`.
     * @return array Transformed output.
     */
    public function transform(array $data): array
    {
        $value = trim($data['value']);

        // Get 'before' and 'after' values, defaulting to an empty string if not provided
        $before = trim($this->getOption(self::BEFORE_PARAM, ''));
        $after = trim($this->getOption(self::AFTER_PARAM, ''));

        // Concatenate the values with a single space between them
        $transformedValue = $value ? trim("{$before} {$value} {$after}") : $value;

        return [
            'value' => $transformedValue,
        ];
    }
}
