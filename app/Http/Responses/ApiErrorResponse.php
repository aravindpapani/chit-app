<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Throwable;

class ApiErrorResponse implements Responsable
{
    public function __construct(
        private ?Throwable $exception = null,
        private array $errors,
        private int $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        private array $headers = []
    ) {}

    /**
     * @param  $request
     * @return \Symfony\Component\HttpFoundation\Response|void
     */
    public function toResponse($request)
    {
        $responseData = [
            'status' => 'error', 
            'code' => $this->code, 
            'data' => [],
            'error' => []
        ];
        if (count($this->errors) === 1) {
            $responseData['message'] = reset($this->errors);
            $responseData['error'][] = [
                'code' => key($this->errors),
                'message' => reset($this->errors)
            ];
        } else {
            foreach($this->errors as $key => $value) {
                $responseData['error'][] = [
                    'code' => $key,
                    'message' => $value
                ];
            }
        }

        if (! is_null($this->exception) && config('app.debug')) {
            $responseData['debug'] = [
                'message' => $this->exception->getMessage(),
                'file'    => $this->exception->getFile(),
                'line'    => $this->exception->getLine(),
                'trace'   => $this->exception->getTraceAsString()
            ];
        }

        return response()->json($responseData, $this->code, $this->headers);
    }
}