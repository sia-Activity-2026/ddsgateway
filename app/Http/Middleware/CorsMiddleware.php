<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $config = config('cors');

        // Get origin from request
        $origin = $request->header('Origin');

        // Check if origin is allowed
        $allowedOrigins = $config['allowed_origins'];
        $isOriginAllowed = in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins);

        // Handle OPTIONS preflight requests
        if ($request->isMethod('OPTIONS')) {
            $response = response('', 200);
        } else {
            $response = $next($request);
        }

        // Set CORS headers if origin is allowed
        if ($isOriginAllowed) {
            $headers = [
                'Access-Control-Allow-Origin' => $origin ?: '*',
                'Access-Control-Allow-Methods' => implode(',', $config['allowed_methods']),
                'Access-Control-Allow-Headers' => implode(',', $config['allowed_headers']),
                'Access-Control-Max-Age' => $config['max_age'],
            ];

            if ($config['supports_credentials']) {
                $headers['Access-Control-Allow-Credentials'] = 'true';
            }

            if (!empty($config['expose_headers'])) {
                $headers['Access-Control-Expose-Headers'] = implode(',', $config['expose_headers']);
            }

            foreach ($headers as $name => $value) {
                if (method_exists($response, 'header')) {
                    $response->header($name, $value);
                } else {
                    $response->headers->set($name, $value);
                }
            }
        }

        return $response;
    }
}
