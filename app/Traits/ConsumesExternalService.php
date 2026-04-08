<?php

namespace App\Traits;
// include the Guzzle Component Library
use GuzzleHttp\Client;


trait ConsumesExternalService
{
    /**
     * Send a request to any service
     * @return string
     */
    // note form params and headers are optional public


    function performRequest($method, $requestUrl, $form_params = [], $headers = [])
    {
        // create a new client request
        $client = new Client([
            'base_uri' => $this->baseUri,
            'connect_timeout' => 5,  // 5 second connection timeout
            'timeout' => 10,         // 10 second request timeout
        ]);

        if (isset($this->secret)) {
            $headers['Authorization'] = $this->secret;
        }

        // perform the request (method, url, formparameters, headers) 
        $response = $client->request(
            $method,
            $requestUrl,
            [
                'form_params' => $form_params,
                'headers' => $headers,
                // Do not throw exceptions on 4xx/5xx; let callers
                // handle the actual status and body from the service.
                // 'http_errors' => false,
]);
        // return the response body contents
        return $response->getBody()->getContents();
    }
}
