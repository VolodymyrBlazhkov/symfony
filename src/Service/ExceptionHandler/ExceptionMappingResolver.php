<?php

namespace App\Service\ExceptionHandler;

use http\Exception\InvalidArgumentException;

class ExceptionMappingResolver
{
    /**
     * @var ExceptionMapping[]
     */
    private array $mappings;

    public function __construct(array $mappings)
    {
        foreach ($mappings as $class => $mapping) {
            if (empty($mapping['code'])) {
                throw new InvalidArgumentException('code is mandatory for class '. $class);
            }

            $this->addMapping(
                $class,
                $mapping['code'],
                $mapping['hidden'] ?? true,
                $mapping['loggable'] ?? false,
            );
        }
    }

    public function resolve(string $trowableClass): ?ExceptionMapping
    {
        $foundMapping = null;

        foreach ($this->mappings as $class => $mapping) {
            if ($trowableClass === $class || is_subclass_of($trowableClass, $class)) {
                $foundMapping = $mapping;
                break;
            }
        }

        return $foundMapping;
    }

    private function addMapping(
        string $class,
        int $code,
        bool $hidden,
        bool $loggable
    ) {
        $this->mappings[$class] = new ExceptionMapping($code, $hidden, $loggable);
    }
}