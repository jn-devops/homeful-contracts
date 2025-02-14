<?php

namespace App\Mappings\Transformers;

/**
 * Class NumberPercentTransformer
 *
 * Converts a number into a percentage representation.
 * Uses Laravel's `Number::percentage()` helper method.
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
 * ## **Available Options**
 * | Option   | Type    | Default | Description                     |
 * |----------|--------|---------|---------------------------------|
 * | precision | int    | `2`     | Number of decimal places       |
 *
 * Example with precision:
 * ```php
 * $transformer = new NumberPercentTransformer('precision=1');
 * $result = $transformer->transform(['value' => 0.1234]);
 * echo $result['value']; // "12.3%"
 * ```
 */
class NumberPercentTransformer extends NumberTransformer
{
    /**
     * @var string The command executed via Laravel's `Number` helper.
     */
    protected string $command = 'percentage';
}
