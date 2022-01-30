<?php

namespace Seffeng\LaravelSLS\Helpers;

class ArrayHelper
{
    /**
     * @author zxf
     * @date    2019年11月25日
     *
     * @param array      $array
     * @param int|string $key
     * @param mixed      $default
     *
     * @return array|string
     */
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }

        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            return is_null($array[$key]) ? $default : $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key   = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            // this is expected to fail if the property does not exist, or __get() is not implemented
            // it is not reliably possible to check whether a property is accessable beforehand
            return $array->{$key};
        }

        if (is_array($array)) {
            return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
        }

        return $default;
    }

    /**
     * @author zxf
     * @date    2020年4月26日
     *
     * @return number
     */
    public static function getDepth(array $array)
    {
        $maxDepth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = self::getDepth($value) + 1;

                if ($depth > $maxDepth) {
                    $maxDepth = $depth;
                }
            }
        }

        return $maxDepth;
    }
}
