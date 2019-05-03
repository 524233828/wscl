<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2017/11/8
 * Time: 15:05
 */

namespace App\Http\Middleware;

use App\AdminUser;
use App\Api\Constant\ErrorCode;
use Illuminate\Http\Request;

class LoginCheck
{

    public function handle(Request $request, \Closure $next, $gurad = null)
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
