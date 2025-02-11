<?php

namespace App\Mappings\Processors;

use App\Mappings\Pipelines\{CastPipe, TransformPipe};
use Illuminate\Pipeline\Pipeline;
use App\Models\Mapping;

/**
 * AbstractMappingProcessor
 *
 * This abstract class defines the common logic for processing mapping values using a pipeline.
 * Subclasses implement the data source-specific logic to retrieve the initial value, and the
 * pipeline handles applying any transformations and casting the value to its intended type.
 *
 * **Core Steps:**
 * 1. **Retrieve the Initial Value:** The subclass retrieves the data based on the mapping’s path.
 * 2. **Apply Transformers:** The pipeline processes any defined transformers (e.g., `UpperCaseTransformer`).
 * 3. **Cast the Final Value:** The pipeline casts the processed value to the correct type (e.g., string, integer).
 *
 * **Design Overview:**
 * - Subclasses implement `getInitialValue()`, which fetches the initial data from its source
 *   (e.g., an array, configuration, or request).
 * - The `process()` method delegates transformation and casting to pipeline pipes (`TransformPipe` and `CastPipe`).
 *
 * **Example Usage:**
 * ```php
 * $processor = new ArrayMappingProcessor($data, $mapping);
 * $result = $processor->process();
 * ```
 */
abstract class AbstractMappingProcessor
{
    protected Mapping $mapping;

    /**
     * Initialize the mapping processor.
     *
     * @param Mapping $mapping The mapping configuration specifying the data path, transformers, and type casting.
     */
    public function __construct(Mapping $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * Process the mapping and return the final value.
     *
     * Steps:
     * - **Step 1:** Retrieve the initial value (subclass-specific logic).
     * - **Step 2:** Apply the transformers (if any) using the `TransformPipe`.
     * - **Step 3:** Cast the final value using the `CastPipe`.
     *
     * The pipeline ensures that the value passes sequentially through transformation and type casting.
     *
     * @return mixed The processed and casted value.
     */
    public function process(): mixed
    {
        // Step 1: Get the initial value (delegated to the subclass)
        $value = $this->getInitialValue();

        // Step 2 and Step 3: Apply transformations and cast the value using the pipeline
        return app(Pipeline::class)
            ->send($value)
            ->through([
                new TransformPipe($this->mapping),
                new CastPipe($this->mapping),
            ])
            ->thenReturn();
    }

    /**
     * Retrieve the initial value for the mapping.
     *
     * This is the only step that subclasses are responsible for implementing.
     * The subclass will retrieve the value based on the mapping's path and source.
     *
     * For example:
     * - `ArrayMappingProcessor` retrieves values from arrays.
     * - `ConfigMappingProcessor` retrieves values from configuration files.
     * - `SearchParamsMappingProcessor` retrieves values from URL search parameters.
     *
     * @return mixed The initial value to be processed.
     */
    abstract protected function getInitialValue(): mixed;
}
