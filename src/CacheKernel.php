<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CacheKernel extends HttpCache
{
    protected function getOptions()
    {
        return [
            'debug'                  => false,
            'default_ttl'            => 0,
            'private_headers'        => array('Authorization', 'Cookie'),
            'allow_reload'           => false,
            'allow_revalidate'       => false,
            'stale_while_revalidate' => 2,
            'stale_if_error'         => 60,
            'trace_level' => 'full',
            'trace_header' => 'X-Symfony-Cache-New'
        ];
    }

    protected function invalidate(Request $request, $catch = false)
    {
        if ('PURGE' !== $request->getMethod()) {
            return parent::invalidate($request, $catch);
        }

        if ('127.0.0.1:8000' !== $request->getClientIp()) {
            return new Response(
                'Invalid HTTP method',
                400
            );
        }

        $response = new Response();

        if ($this->getStore()->purge($request->getUri())) {
            $response->setStatusCode(200, 'Purge');
        } else {
            $response->setStatusCode(404, 'Not Found');
        }

        return $response;
    }
}
