<?php


if (!function_exists('getFiltersFromRequest')) {
    function getFiltersFromRequest($key = 'filters')
    {
        $filters = [];

        if (request()->has($key) && is_string($queryString = request()->query($key))) {
            $keyValuePairs = explode(";", $queryString);

            foreach ($keyValuePairs as $pair) {
                $pair = explode(":", $pair);

                if (count($pair) !== 2)
                    continue;

                $key = $pair[0];
                $value = $pair[1];

                if (strpos($value, ',')) {
                    $value = explode(",", $value);
                }

                $filters[$key] = $value;
            }
        }

        return $filters;
    }
}

if (!function_exists('isDateFromFormat')) {
    function isDateFromFormat($string)
    {
        $formats = ['Y-m-d', 'd-m-Y', 'Y/m/d'];
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $string);
            if ($date) {
                return true;
            }
        }

        return false;
    }
}