<?php

namespace App\Mappings\Transformers;

/**
 * Class NumberAbbreviateTransformer
 *
 * Abbreviates large numbers into a compact, human-readable format.
 * Uses Laravel's `Number::abbreviate()` helper method.
 *
 * ---
 *
 * ## **Example Usage**
 *
 * ```php
 * $transformer = new NumberAbbreviateTransformer();
 * $result = $transformer->transform(['value' => 1000000]);
 * echo $result['value']; // "1M"
 * ```
 *
 * ---
 *
 * ## **Available Options**
 * | Option    | Type  | Default | Description                                         |
 * |----------|------|---------|-----------------------------------------------------|
 * | precision | int  | `1`     | Number of decimal places to include in abbreviation |
 *
 * **Examples with Options**
 * ```php
 * $transformer = new NumberAbbreviateTransformer('precision=2');
 * $result = $transformer->transform(['value' => 1500000]);
 * echo $result['value']; // "1.50M"
 * ```
 *
 * ---
 *
 * ## **Use Cases**
 * - **Displaying large numbers in UI dashboards** (e.g., "1.2M users" instead of "1,200,000").
 * - **Social media & analytics applications** (e.g., "5.3K likes" instead of "5,300").
 * - **Financial reports & statistics** where abbreviated formats improve readability.
 */
class NumberAbbreviateTransformer extends NumberTransformer
{
    /**
     * @var string The Laravel `Number` helper method used for transformation.
     */
    protected string $command = 'abbreviate';
}
