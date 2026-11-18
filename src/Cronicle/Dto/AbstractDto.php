<?php

namespace NGSOFT\Cronicle\Dto;

/**
 * Base hydratable / serializable DTO for Cronicle API payloads.
 *
 * @phpstan-consistent-constructor
 */
abstract class AbstractDto implements \JsonSerializable
{
    public function __construct() {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $instance = new static();
        $instance->hydrate($data);

        return $instance;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(bool $omitNull = true): array
    {
        $result = [];

        foreach (get_object_vars($this) as $key => $value)
        {
            if ($omitNull && null === $value)
            {
                continue;
            }

            if ($value instanceof self)
            {
                $result[$key] = $value->toArray($omitNull);
                continue;
            }

            if (is_array($value))
            {
                $items        = [];

                foreach ($value as $index => $item)
                {
                    $items[$index] = $item instanceof self ? $item->toArray($omitNull) : $item;
                }
                $result[$key] = $items;
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function hydrate(array $data): void
    {
        foreach ($data as $key => $value)
        {
            if ( ! property_exists($this, $key) || null === $value)
            {
                continue;
            }

            $this->{$key} = $this->cast($key, $value);
        }
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        return $value;
    }
}
