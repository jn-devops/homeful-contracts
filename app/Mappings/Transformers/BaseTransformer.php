<?php

namespace App\Mappings\Transformers;

use League\Fractal\TransformerAbstract;

abstract class BaseTransformer extends TransformerAbstract
{
    protected array $options = [];

    /**
     * Constructor to parse query string options.
     *
     * @param string|null $option A query-string formatted string (e.g., "after=10&until=5").
     */
    public function __construct(?string $option = '')
    {
        // Parse the query string into an associative array
        parse_str($option, $this->options);
    }

//    public function __construct(array $options = [])
//    {
//        $this->options = $options;
//    }

    /**
     * Retrieve an option by key, or return the default if it does not exist.
     * Supports flags like `read_only` by treating them as `true` if no value is specified.
     *
     * @param string $key The option key to retrieve.
     * @param mixed $default The default value to return if the key is not found.
     * @return mixed
     */
    protected function getOption(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->options)) {
            // If the option has an empty value, treat it as a boolean flag set to `true`
            return $this->options[$key] === '' ? true : $this->options[$key];
        }

        return $default;
    }
}
