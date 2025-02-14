<?php

namespace App\Mappings\Transformers;

/**
 * Class NumberFormatTransformer
 *
 * Formats a number according to locale-based number formatting.
 * Uses Laravel's `Number::format()` helper method.
 *
 * ---
 *
 * ## **Example Usage**
 *
 * ```php
 * $transformer = new NumberFormatTransformer();
 * $result = $transformer->transform(['value' => 1000000]);
 * echo $result['value']; // "1,000,000"
 * ```
 *
 * ---
 *
 * ## **Available Options**
 * | Option   | Type    | Default | Description                     |
 * |----------|--------|---------|---------------------------------|
 * | precision | int    | `0`     | Number of decimal places       |
 * | locale    | string | `en`    | Locale setting (e.g., "fr", "de") |
 *
 * Example with precision:
 * ```php
 * $transformer = new NumberFormatTransformer('precision=2');
 * $result = $transformer->transform(['value' => 1000]);
 * echo $result['value']; // "1,000.00"
 * ```
 */
class NumberFormatTransformer extends NumberTransformer
{
    /**
     * @var string The command executed via Laravel's `Number` helper.
     */
    protected string $command = 'format';
}
