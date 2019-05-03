<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2017/11/8
 * Time: 15:05
 */

namespace Middleware;

use App\AdminUser;
use App\Api\Constant\ErrorCode;
use Illuminate\Support\Facades\Auth;
use Psr\Http\Message\ServerRequestInterface;

class LoginCheck
{

    public function handle(ServerRequestInterface $request, \Closure $next, $gurad = null)
    {
        if (!empty(AdminUser::$user)) {
            $response = $next($request);
        } else {
            return \response()->json([
                ErrorCode::msg(ErrorCode::USER_NOT_LOGIN),
                ErrorCode::USER_NOT_LOGIN,
                [],
                200
            ]);
        }

        return $response;
    }
}
