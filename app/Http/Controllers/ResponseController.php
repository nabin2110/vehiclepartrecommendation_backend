<?php

namespace App\Http\Controllers;

abstract class ResponseController
{
    public function success(array|object $data = [],?string $message = '',int $statusCode = 200){
        return response()->json([
            'success'=>true,
            'data'=>$data,
            'message'=>$message
        ],$statusCode);
    }
    public function error(string $message,int $statusCode = 500){
        return response()->json([
            'success'=>false,
            'message'=>$message
        ],$statusCode);
    }
}
