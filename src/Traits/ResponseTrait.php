<?php

namespace NamaaSolutions\CrudGenerator\Traits;

trait ResponseTrait
{
    public function SuccessResponse($data = [], $code = 200, $message = '', $hint = '', $errors = null)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'hint' => $hint,
            'success' => true,
            'result' => $data,
            'errors' => $errors,
        ]);
    }

    public function ErrorResponse($message = '', $code = 400, $errors = null, $hint = '', $data = null)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'hint' => $hint,
            'success' => false,
            'result' => $data,
            'errors' => $errors,
        ], $code);
    }
}
