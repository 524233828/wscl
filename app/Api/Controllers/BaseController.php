<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/10/27
 * Time: 22:45
 */

namespace App\Api\Controllers;

use App\Api\Constant\ErrorCode;
use App\Controller;

class BaseController extends Controller
{
    protected function response($data, $msg = '', $code = 1, $status = 200, array $header = [])
    {
        $msg = $msg ? $msg : ErrorCode::msg($code);

        $result['data'] = !is_array($data) && !is_null(json_decode($data)) ? json_decode($data, true) : $data;
        $result['msg'] = $msg;
        $result['code'] = $code;

        return \response()->json($result, $status, $header);
    }
}