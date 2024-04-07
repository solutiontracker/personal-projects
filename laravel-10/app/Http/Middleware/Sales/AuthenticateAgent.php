<?php

namespace App\Http\Middleware\Sales;

use App\Eventbuizz\Repositories\Sales\SaleAgentRepository;
use App\Http\Helpers\HttpHelper;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticateAgent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $requestUser = $request->user();
        $saleAgent = app(SaleAgentRepository::class)->getAgentByColumn('id', $requestUser->id);
        if (!isset($saleAgent)) {
            return HttpHelper::errorJsonResponse('Insufficient privileges to perform operation', 'Authorization Error');
        }

        return $next($request);
    }
}
