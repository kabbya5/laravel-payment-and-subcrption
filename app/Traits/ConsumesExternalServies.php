<?php 

namespace App\Traits;
use GuzzleHttp\Client;

trait ConsumesExternalServies{
    
    public function makeRequest($method, $requestUrl,$queryParams= [],
    $formParam = [],$headers = [], $isJsonRequest = false)
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        if(method_exists($this,'resolveAuthorization')){
            $this->resolveAuthorization($queryParams,$formParam,$headers);
        }

        $response = $client->request($method, $requestUrl,[
            $isJsonRequest ? 'json' :'form_params' => $formParam,
            'headers' => $headers,
            'query' => $queryParams,
        ]);

        $response = $response->getBody()->getContents();

        $response = $this->decodeResponse($response);

        return $response;
    }
}