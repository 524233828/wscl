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
    const PARAMS_ERROR = -1;
    const SYSTEM_ERROR = -2;
    const LOGIN_FAIL = 1000;
    const USER_NOT_LOGIN = 1002;
    const FORBIDDEN = 1003;
    const COMPANY_NOT_FOUND = 2000;

    static $error = [

        self::SUCCESS => ["处理成功", Response::HTTP_OK],
        self::PARAMS_ERROR => ["参数错误", Response::HTTP_OK],
        self::SYSTEM_ERROR => ["系统错误", Response::HTTP_OK],
        self::LOGIN_FAIL => ["登录失败", Response::HTTP_BAD_REQUEST],
        self::USER_NOT_LOGIN => ["未登录", Response::HTTP_BAD_REQUEST],
        self::FORBIDDEN => ["无权限操作", Response::HTTP_BAD_REQUEST],
        self::COMPANY_NOT_FOUND => ["污水厂不存在", Response::HTTP_BAD_REQUEST]
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