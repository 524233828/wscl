<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/10/27
 * Time: 22:32
 */

namespace App\Api\Constant;


use Illuminate\Http\Response;

class ErrorCode
{

    //错误码
    const SUCCESS = 1;
    const GET_ACCESS_TOKEN_FAIL = 1000;

    static $error = [

        self::SUCCESS => ["处理成功", Response::HTTP_OK],
        self::GET_ACCESS_TOKEN_FAIL => ["获取access_token失败", Response::HTTP_BAD_REQUEST]
    ];


    /**
     * 返回错误代码的描述信息
     *
     * @param int    $code        错误代码
     * @param string $otherErrMsg 其他错误时的错误描述
     * @return string 错误代码的描述信息
     */
    public static function msg($code, $otherErrMsg = '')
    {
        if (isset(self::$error[$code][0])) {
            return self::$error[$code][0];
        }

        return $otherErrMsg;
    }

    /**
     * 返回错误代码的Http状态码
     * @param int $code
     * @param int $default
     * @return int
     */
    public static function status($code, $default = 200)
    {
        if (isset(self::$error[$code][1])) {
            return self::$error[$code][1];
        }

        return $default;
    }

    public static function getCode($code)
    {
        return isset(self::$error[$code])?self::$error[$code]:false;
    }

    public static function error($code)
    {
        throw new \Exception(self::msg($code), $code);
    }
}