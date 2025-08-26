<?php

if (!function_exists('api_success')) {
    function api_success($data = null, $meta = null, int $status = 200)
    {
        return response()->json([
            'data' => $data,
            'meta' => $meta,
            'errors' => null
        ], $status);
    }
}

if (!function_exists('api_error')) {
    function api_error(string $code, string $detail, int $status = 400, ?string $field = null)
    {
        return response()->json([
            'data' => null,
            'meta' => null,
            'errors' => [[
                'code' => $code,
                'detail' => $detail,
                'field' => $field
            ]]
        ], $status);
    }
}
