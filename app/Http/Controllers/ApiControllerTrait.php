<?php
namespace App\Http\Controllers;
use Illuminate\Http\Response as IlluminateResponse;
trait ApiControllerTrait
{
    protected $statusCode = IlluminateResponse::HTTP_OK;
    protected $errorString = "ERROR";
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    public function error($data, $headers = []){
        $responseContent=["status"=>$this->errorString, "data"=>$data];
        return response()->json($responseContent, $this->getStatusCode(), $headers);
    }
}