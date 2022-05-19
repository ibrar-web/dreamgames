<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DomainCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
     public function handle($request, Closure $next)
    {
        $allowedHosts = explode(',', env('ALLOWED_DOMAINS'));

        $requestHost = parse_url($request->headers->get('origin'),  PHP_URL_HOST);

        if(!app()->runningUnitTests()) {
            if(!\in_array($requestHost, $allowedHosts, false)) {
                $requestInfo = [
                    'host' => $requestHost,
                    'ip' => $request->getClientIp(),
                    'url' => $request->getRequestUri(),
                    'agent' => $request->header('User-Agent'),
                ];
               // event(new UnauthorizedAccess($requestInfo));

                //throw new SuspiciousOperationException('This host is not allowed');
            }
        }

        return $next($request);
    }
    public function UnauthorizedAccess($event)
    {
        $data = $event->data;
        Log::warning('access_from_unauthorized_domain_' . date('Y-m-d_H:i:s'), $data);
        
    }

}
