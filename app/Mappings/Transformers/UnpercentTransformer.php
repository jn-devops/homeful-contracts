<?php

namespace App\Mappings\Transformers;

use League\Fractal\TransformerAbstract;

/**
 * Class UnPercentTransformer
 *
 * This transformer converts a fractional percentage value (stored as a decimal)
 * to its whole number equivalent by multiplying the value by 100.
 *
 * ## Purpose
 *
 * In many systems, percentage values are stored as fractions (for example, 0.60 for 60%)
 * to maintain precision during arithmetic operations. However, for display or reporting,
 * it's more conventional to show the value as a whole percentage (e.g., 60%).
 *
 * ## How It Works
 *
 * - **Input:** The transformer expects an array with a key `value` holding the fractional number.
 *   For example: `['value' => 0.60]`
 *
 * - **Processing:** It casts the input to a float (if it isn't already) and multiplies it by 100.
 *
 * - **Output:** Returns an array with the key `value` containing the converted whole number.
 *   For example: `['value' => 60.0]`
 *
 * ## Example Usage
 *
 * ```php
 * $transformer = new UnPercentTransformer();
 * $result = $transformer->transform(['value' => 0.85]);
 * // $result['value'] is now 85.0
 * ```
 *
 * ## Integration
 *
 * This transformer is typically used as part of a data transformation pipeline where
 * percentage values stored in fractional format need to be converted before further processing
 * or display (e.g., formatting as "85%").
 *
 * @package App\Mappings\Transformers
 */
class UnpercentTransformer extends BaseTransformer
{
    /**
     * Transform a fractional percentage to its whole percentage equivalent.
     *
     * @param array $data The input data array containing the 'value' key.
     *                    Example: ['value' => 0.60]
     *
     * @return array The transformed array with the 'value' multiplied by 100.
     *               Example: ['value' => 60.0]
     */
    public function transform(array $data): array
    {
        return [
            'value' => (float)$data['value'] * 100,
        ];
    }
}
