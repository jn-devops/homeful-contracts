<?php

namespace App\Classes;

use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

abstract class BarkerType implements JsonSerializable
{
    /**
     * The key identifier for the type.
     *
     * @var string
     */
    public string $key;

    /**
     * The name of the type.
     *
     * @var string
     */
    public string $name;

    /**
     * The type's description.
     *
     * @var string
     */
    public string $description;

    /**
     * Create a new type instance.
     *
     * @param  string $key
     * @param  string $name
     * @return void
     */
    public function __construct(string $key, string $name)
    {
        $this->key = $key;
        $this->name = $name;
    }

    /**
     * Describe the type.
     *
     * @param  string $description
     * @return $this
     */
    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the JSON serializable representation of the object.
     *
     * @return array
     */
    #[ArrayShape(['key' => "string", 'name' => "string", 'description' => "string"])]
    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'name' => __($this->name),
            'description' => __($this->description),
        ];
    }

    /**
     * @return string
     */
    public static function enum(): string
    {
        return static::$enum;
    }

    /**
     * This method essentially creates the payload for
     * consumption in Inertia as props object.
     * Never knew that the following
     * "static::enum()::cases()"
     * would work! :-) :-)
     *
     * @return array
     */
    public static function records(array $filter = []): array
    {
        $recs = [];
        foreach (static::enum()::cases() as $case) {
            tap(new static($case->key(), $case->name()), function (self $rec) use (&$recs, $case) {
                $rec->description($case->description());
                $recs[$case->key()] = $rec->jsonSerialize();
            });
        }

        return $filter
            ? array_intersect_key($recs, array_flip($filter))
            : $recs;
    }
}
