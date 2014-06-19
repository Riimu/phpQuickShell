<?php

namespace Riimu\QuickShell;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class ValueEncoder
{
    private $depth;
    private $maxDepth = 20;
    private $baseIndent = 0;
    private $indent = 2;

    public function encode($value)
    {
        $this->depth = 0;
        return $this->encodeValue($value);
    }

    private function encodeValue($value)
    {
        switch (true) {
            case is_bool($value):
                return $this->encodeBoolean($value);
            case is_int($value):
                return $this->encodeInteger($value);
            case is_float($value):
                return $this->encodeFloat($value);
            case is_string($value):
                return $this->encodeString($value);
            case is_array($value):
                return $this->encodeArray($value);
            case is_object($value):
                return $this->encodeObject($value);
            case is_resource($value):
                return $this->encodeResource($value);
            case $value === null:
                return 'null';
            default:
                return 'Unknown value type';
        }
    }

    private function encodeBoolean($boolean)
    {
        return $boolean ? 'true' : 'false';
    }

    private function encodeInteger($integer)
    {
        return (string) $integer;
    }

    private function encodeFloat($float)
    {
        if (is_infinite($float) || is_nan($float)) {
            return (string) $float;
        }

        return strpos($float, '.') !== false ? (string) $float : "$float.0";
    }

    private function encodeString($string)
    {
        return '"' . $string . '"';
    }

    private function encodeArray($array)
    {
        if ($this->depth > $this->maxDepth && $this->maxDepth !== false) {
            return '[ ... ]';
        } elseif ($array === []) {
            return '[]';
        }

        $indent = $this->getIndent(++$this->depth);
        $pairs = $this->getAlignedPairs($array, ' => ');
        $this->depth--;

        return '[' . PHP_EOL .
            $indent . implode(',' . PHP_EOL . $indent, $pairs) . PHP_EOL .
            $this->getIndent($this->depth) . ']';
    }

    private function encodeObject($object)
    {
        $name = "object (" . get_class($object) . ")";

        if ($this->depth > $this->maxDepth && $this->maxDepth !== false) {
            return $name . ' { ... }';
        }

        $properties = get_object_vars($object);

        if ($properties === []) {
            return "$name { }";
        }

        $indent = $this->getIndent(++$this->depth);
        $pairs = $this->getAlignedPairs($properties, ' : ');
        $this->depth--;

        return $name . ' {' . PHP_EOL .
            $indent . implode(',' . PHP_EOL . $indent, $pairs) . PHP_EOL .
            $this->getIndent($this->depth) . '}';
    }

    private function getAlignedPairs($array, $glue)
    {
        $maxKeyLength = max(array_map('strlen', array_keys($array)));
        $pairs = [];

        foreach ($array as $key => $value) {
            $pairs[] = str_pad($key, $maxKeyLength) . $glue .
                $this->encodeValue($value);
        }

        return $pairs;
    }

    private function encodeResource($resource)
    {
        if (get_resource_type($resource) === 'GMP integer') {
            return 'resource (GMP integer) {' . PHP_EOL .
                $this->getIndent($this->depth + 1) . 'value : "' . gmp_strval($resource) . '"' . PHP_EOL .
                $this->getIndent($this->depth) . '}';
        } else {
            return 'resource (' . get_resource_type($resource) . ')';
        }
    }

    private function getIndent($depth)
    {
        $base = is_int($this->baseIndent) ? str_repeat(' ', $this->baseIndent) : $this->baseIndent;
        $indent = is_int($this->indent) ? str_repeat(' ', $this->indent) : $this->indent;
        return $base . str_repeat($indent, $depth);
    }
}
