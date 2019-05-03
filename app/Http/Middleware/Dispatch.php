<?php
namespace Middleware;

use App\AdminUser;
use App\Constant\JWTKey;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface;

class Dispatch
{

    public function handle(ServerRequestInterface $request, \Closure $next)
    {

        $token = $request->getHeaderLine('Authorization');
        if ($token) {
            // 只做拆分获取用户ID，不判断可用性
            try {
                $decoded = (array)JWT::decode($token, JWTKey::KEY, [JWTKey::ALG]);
                $user_id = isset($decoded['aud']) ? (string)$decoded['aud'] : 0;
                if (!empty($user_id)) {
                    $user = AdminUser::where("id", "=", $user_id)->get();
                    if (!empty($user)) {
                        AdminUser::$user = $user;
                    }
                }
            } catch (\Exception $e) {
                AdminUser::$user = null;
            }
        }

        try {
            $response = $next($request);
        } catch (\Exception $e) {

        }

        return $response;
    }
}
