<?php

namespace App\Mappings\Pipelines;

use Brick\Math\Exception\MathException;
use App\Models\Mapping;
use Closure;

/**
 * Class CastPipe
 *
 * This pipeline step is responsible for converting or "casting" a given value
 * to its intended data type as defined in the `Mapping` model. The casting
 * logic handles basic types (e.g., string, integer, float) as well as more
 * complex types like arrays and JSON, using the appropriate transformation logic.
 */
class CastPipe
{
    protected Mapping $mapping;

    /**
     * Initialize the pipe with the given mapping configuration.
     *
     * @param Mapping $mapping The mapping that defines the target data type.
     */
    public function __construct(Mapping $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * Cast the input value to its intended type and pass it to the next pipe.
     *
     * This method applies type casting based on the `MappingType` defined in
     * the mapping. If any errors occur during casting (e.g., when using Brick\Money),
     * they are thrown as exceptions for better debugging.
     *
     * @param mixed $value The value to be cast.
     * @param Closure $next The next step in the pipeline.
     * @return mixed The casted value passed to the next pipeline step.
     * @throws MathException If casting fails (e.g., during Money-related operations).
     */
    public function handle(mixed $value, Closure $next): mixed
    {
        // Cast the value to the appropriate type as defined in the mapping
        $castedValue = $this->mapping->type->castValue($value);

        // Pass the casted value to the next pipeline step
        return $next($castedValue);
    }
}
