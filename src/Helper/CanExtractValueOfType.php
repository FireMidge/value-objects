<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Helper;

use FireMidge\ValueObject\Exception\ConversionError;
use Throwable;

trait CanExtractValueOfType
{
    private function getIntValueOfOther(int|float|object $other) : int
    {
        if (is_int($other)) {
            return $other;
        }

        if (is_float($other)) {
            return (int) $other;
        }

        $methodsToSearchFor = ['toInt', 'toFloat', 'toDouble', 'toNumber'];

        foreach ($methodsToSearchFor as $method) {
            if (method_exists($other, $method)) {
                return (int) $other->$method();
            }
        }

        throw ConversionError::couldNotConvert(
            $other,
            'int',
            sprintf(
                'Make sure the class has one of these methods: "%s"',
                implode('", "', $methodsToSearchFor)
            )
        );
    }

    private function getFloatValueOfOther(float|int|object $other) : float
    {
        if (is_float($other)) {
            return $other;
        }

        if (is_int($other)) {
            return (float) $other;
        }

        $methodsToSearchFor = ['toFloat', 'toDouble', 'toInt', 'toNumber'];

        foreach ($methodsToSearchFor as $method) {
            if (method_exists($other, $method)) {
                return (float) $other->$method();
            }
        }

        throw ConversionError::couldNotConvert(
            $other,
            'float',
            sprintf(
                'Make sure the class has one of these methods: "%s"',
                implode('", "', $methodsToSearchFor)
            )
        );
    }

    private function getStringValueOfOther(string|object $other) : string
    {
        if (is_string($other)) {
            return $other;
        }

        $methodsToSearchFor = ['toString', 'toText'];

        foreach ($methodsToSearchFor as $method) {
            if (method_exists($other, $method)) {
                return (string) $other->$method();
            }
        }

        try {
            return (string) $other;
        } catch (Throwable) {
            throw ConversionError::couldNotConvert(
                $other,
                'string',
                sprintf(
                    'Make sure the class has one of these methods: "%s", "__toString"',
                    implode('", "', $methodsToSearchFor)
                )
            );
        }
    }
}