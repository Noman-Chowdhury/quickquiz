<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function basicSuccessResponse(string $message = 'Congratulations !', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], $statusCode);
    }

    public function basicErrorResponse(string $message = 'Something Went Wrong !'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ]);
    }

    public function tryCatch($model, $validated_data, $action, $success_message): JsonResponse
    {
        DB::beginTransaction();
        try {
            if (is_object($model)) {
                $model->$action($validated_data);
            } else {
                $model::$action($validated_data);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->basicErrorResponse($exception->getMessage());
        }
        return $this->basicSuccessResponse($success_message);
    }
}
