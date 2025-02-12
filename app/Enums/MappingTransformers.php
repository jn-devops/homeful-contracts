<?php

namespace App\Enums;

use App\Mappings\Transformers\Test\ReverseStringTransformer;
use App\Mappings\Transformers\NumberSpellTransformer;
use App\Mappings\Transformers\ToMajorUnitTransformer;
use App\Mappings\Transformers\TitleCaseTransformer;
use App\Mappings\Transformers\UpperCaseTransformer;
use App\Mappings\Transformers\CurrencyTransformer;
use App\Mappings\Transformers\ConcatTransformer;
use App\Mappings\Transformers\JoinTransformer;
use Homeful\Common\Traits\EnumUtils;
use Illuminate\Support\Str;

/**
 * Enum MappingTransformers
 *
 * Centralized enum for managing and resolving transformer classes dynamically.
 * Provides utility methods for validation and dynamic resolution of transformer classes.
 *
 * **Core Methods:**
 * - `default()`: Returns the default transformer (`UPPER_CASE`).
 * - `find()`: Resolves a transformer based on its short or full name.
 * - `isValid()`: Validates if a given transformer name is recognized.
 *
 * **Example Usage:**
 * ```php
 * $transformerEnum = MappingTransformers::find('Currency');
 * $transformerClass = $transformerEnum->transformer();
 * $transformedValue = app($transformerClass)->transform(['value' => '1000'])['value'];
 * ```
 *
 * **Form Validation:**
 * Use `MappingTransformers::rule()` in form requests:
 * ```php
 * public function rules(): array {
 *     return ['transformer' => ['required', MappingTransformers::rule()]];
 * }
 * ```
 */
enum MappingTransformers
{
    use EnumUtils;

    case UPPER_CASE;
    case TITLE_CASE;
    case TO_MAJOR_UNIT;
    case CURRENCY;
    case NUMBER_SPELL;
    case CONCAT;
    case JOIN;
    case REVERSE_STRING;

    /**
     * Returns the default transformer (`UPPER_CASE`).
     *
     * @return self
     */
    static function default(): self
    {
        return self::UPPER_CASE;
    }

    /**
     * Return the fully qualified class name of the transformer.
     *
     * @return string
     */
    public function transformer(): string
    {
        return match ($this) {
            self::UPPER_CASE => UpperCaseTransformer::class,
            self::TITLE_CASE => TitleCaseTransformer::class,
            self::TO_MAJOR_UNIT => ToMajorUnitTransformer::class,
            self::CURRENCY => CurrencyTransformer::class,
            self::NUMBER_SPELL => NumberSpellTransformer::class,
            self::CONCAT => ConcatTransformer::class,
            self::JOIN => JoinTransformer::class,
            self::REVERSE_STRING => ReverseStringTransformer::class
        };
    }

    /**
     * Dynamically resolve an enum case using its name.
     *
     * @param string $name The name of the transformer (e.g., `UPPER_CASE`).
     * @return self|null
     */
    public static function fromName(string $name): ?self
    {
        return defined("self::$name") ? constant("self::$name") : null;
    }

    /**
     * Find the transformer enum case based on its short or full name.
     *
     * **Supports:**
     * - Short names (e.g., `Currency`, `UpperCase`, `UPPER_CASE`)
     * - Fully qualified class names
     *
     * @param string $transformerName The short or full name of the transformer.
     * @return self|null The matching enum case or null if not found.
     */
    public static function find(string $transformerName): ?self
    {
        // Direct match first using the enum case names (e.g., fully capitalized `UPPER_CASE`)
        if ($enum = self::fromName($transformerName)) {
            return $enum;
        }

        // Normalize the name for matching (e.g., "Currency" => "CURRENCY")
        $normalized = Str::of($transformerName)
            ->trim()
            ->snake()  // Handle camelCase or PascalCase
            ->upper()  // Ensure case-insensitive match
            ->toString();

        return self::fromName($normalized);
    }

    /**
     * Validate if a given transformer name is valid.
     *
     * @param string $transformerName The transformer name to validate.
     * @return bool True if valid, false otherwise.
     */
    public static function isValid(string $transformerName): bool
    {
        return self::find($transformerName) !== null;
    }
}
