<?php

namespace App\Traits;
// include the Guzzle Component Library
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

trait ConsumesExternalService
{
    /**
     * Guzzle client instance for reuse
     * @var Client
     */
    protected $client;

    /**
     * Get or create the Guzzle client
     * @return Client
     */
    protected function getClient()
    {
        if (!$this->client) {
            $this->client = new Client([
                'base_uri' => $this->baseUri,
                'timeout' => 30, // 30 seconds timeout
                'connect_timeout' => 10, // 10 seconds connect timeout
                'http_errors' => false, // Don't throw on 4xx/5xx
            ]);
        }
        return $this->client;
    }

    /**
     * Send a request to any service
     * @return string
     */
    function performRequest($method, $requestUrl, $form_params = [], $headers = [])
    {
        $client = $this->getClient();

        if (isset($this->secret)) {
            $headers['Authorization'] = $this->secret;
        }

        $options = [
            'headers' => $headers,
        ];

        // Use JSON for data if it's an array/object, otherwise form params
        if (!empty($form_params)) {
            if (is_array($form_params) && isset($headers['Content-Type']) && $headers['Content-Type'] === 'application/json') {
                $options['json'] = $form_params;
            } else {
                $options['form_params'] = $form_params;
            }
        }

        try {
            $response = $client->request($method, $requestUrl, $options);
            return $response->getBody()->getContents();
        } catch (RequestException $e) {
            // Handle exceptions gracefully
            return json_encode([
                'error' => 'Request failed',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }
}
