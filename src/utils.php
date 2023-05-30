<?php

use Nicolasps\UsersAPI\Meta\HttpResponseCodes;

if (! function_exists('xml_decode')) {
    function xml_decode (string $xml, bool $associative = false)
    {
        if ($associative) {
            return simplexml_load_string($xml);
        }

        return json_decode(json_encode(simplexml_load_string($xml)), true);
    }
}

if (! function_exists('getGlobal')) {
    function getGlobal (string $global): mixed
    {
        return $GLOBALS[$global];
    }
}

if (! function_exists('response')) {
    function response (mixed $data, HttpResponseCodes $httpResponseCode): never
    {
        if (! empty($data)) echo json_encode($data);

        header(HTTPResponseCodes::getHeaderMessage($httpResponseCode));
        exit();
    }
}

if (! function_exists('parseToArray')) {
    function parseToArray (mixed $data): array
    {
        return json_decode(json_encode($data), true);
    }
}

if (! function_exists('array_group_by')) {
    function array_group_by (array $data, mixed $column): array
    {
        $dates = array_column($data, $column);

        foreach ($dates as $date) {
            $value = array_filter(array_map(function ($value) use ($date, $column) {
                if ($date == $value[$column]) return $value;

                return null;
            }, $data));

            $result[$date] = array_values($value);
        }


        return $result ?? $data;
    }
}
