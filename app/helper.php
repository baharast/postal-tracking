<?php

if (!function_exists('resp')) {
    function resp($data = null, $meta = null, $errors = null, $status = 200)
    {
        return response()->json([
            'data'      => $data,
            'meta'      => $meta,
            'errors'    => $errors
        ], $status);
    }
}

if (!function_exists('err')) {
    function err($code, $detail, $field = null)
    {
        return [['code' => $code, 'detail' => $detail, 'field' => $field]];
    }
}
