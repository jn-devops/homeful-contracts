<?php

namespace App\Mappings\Transformers;

/**
 * Class NumberSpellTransformer
 *
 * Converts a numerical value into its spelled-out text equivalent.
 * Uses Laravel's `Number::spell()` helper method.
 *
 * ---
 *
 * ## **Example Usage**
 *
 * ```php
 * $transformer = new NumberSpellTransformer();
 * $result = $transformer->transform(['value' => 100]);
 * echo $result['value']; // "one hundred"
 * ```
 *
 * ---
 *
 * ## **Available Options**
 * | Option  | Type   | Default | Description                              |
 * |---------|--------|---------|------------------------------------------|
 * | after   | int    | `null`  | Spells numbers after a certain threshold |
 * | until   | int    | `null`  | Stops spelling numbers beyond this value |
 *
 * **Examples with Options**
 * ```php
 * $transformer = new NumberSpellTransformer('after=10');
 * $result = $transformer->transform(['value' => 12]);
 * echo $result['value']; // "twelve"
 *
 * $transformer = new NumberSpellTransformer('until=10');
 * $result = $transformer->transform(['value' => 11]);
 * echo $result['value']; // "11" (not spelled out)
 * ```
 *
 * ---
 *
 * ## **Use Cases**
 * - **Check writing & financial applications** (e.g., "one thousand pesos only").
 * - **Generating reports** with numbers in text format.
 * - **Enhancing accessibility** by converting numeric values into readable text.
 */
class NumberSpellTransformer extends NumberTransformer
{
    /**
     * @var string The Laravel `Number` helper method used for transformation.
     */
    protected string $command = 'spell';
}
