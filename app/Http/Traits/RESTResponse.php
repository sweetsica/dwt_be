<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;



// stolen from  hung pho
trait RESTResponse
{
    /** Các status code thường được sử dụng */
    protected const BAD_REQUEST = ResponseAlias::HTTP_BAD_REQUEST;
    protected const UNAUTHORIZED = ResponseAlias::HTTP_UNAUTHORIZED;
    protected const FORBIDDEN = ResponseAlias::HTTP_FORBIDDEN;
    protected const NOT_FOUND = ResponseAlias::HTTP_NOT_FOUND;
    protected const METHOD_NOT_ALLOWED = ResponseAlias::HTTP_METHOD_NOT_ALLOWED;
    protected const OK = ResponseAlias::HTTP_OK;
    protected const CREATED = ResponseAlias::HTTP_CREATED;
    protected const INTERNAL_SERVER_ERROR = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;


    protected mixed $data = null;
    protected string $message = "";
    protected int $apiStatusCode = ResponseAlias::HTTP_OK;
    protected mixed $error = null;

    public function setStatusCode(int $statusCode): self
    {
        $this->apiStatusCode = $statusCode;
        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function setData(mixed $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function setError(mixed $error): self
    {
        $this->error = $error;
        return $this;
    }

    public function successResponse(): JsonResponse
    {
        return response()->json([
            'message' => $this->message,
            'data' => $this->data,
            'status' => $this->apiStatusCode,
        ], $this->apiStatusCode);
    }

    public function errorResponse(): JsonResponse
    {
        return response()->json([
            'message' => $this->message,
            'error' => $this->error,
            'status' => $this->apiStatusCode,
        ], $this->apiStatusCode);
    }
}
