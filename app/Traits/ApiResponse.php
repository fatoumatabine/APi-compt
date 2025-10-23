<?php

namespace App\Traits;

trait ApiResponse
{
    public function successResponse($data, $message = 'Success', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function errorResponse($message, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }

    public function paginatedResponse($data, $message = 'Success', $code = 200)
    {
        $pagination = [
            'currentPage' => $data->currentPage(),
            'totalPages' => $data->lastPage(),
            'totalItems' => $data->total(),
            'itemsPerPage' => $data->perPage(),
            'hasNext' => $data->hasMorePages(),
            'hasPrevious' => $data->currentPage() > 1,
        ];

        $links = [
            'self' => $data->url($data->currentPage()),
            'first' => $data->url(1),
            'last' => $data->url($data->lastPage()),
        ];

        if ($data->hasMorePages()) {
            $links['next'] = $data->nextPageUrl();
        }

        if ($data->currentPage() > 1) {
            $links['previous'] = $data->previousPageUrl();
        }

        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'pagination' => $pagination,
            'links' => $links,
        ], $code);
    }
}
