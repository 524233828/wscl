<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-05-02
 * Time: 15:45
 */

namespace App\Constant;


class JWTKey
{
    const KEY = 'wangyuwangluo';
    const ISS = 'http://ydapi.linghit.com';
    const ALG = 'HS256';

    //第三方平台使用
    const PLATFORM_KEY = 'ydapi.linghit.com_platform';
}