<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019/2/4
 * Time: 23:29
 */

namespace App\Api\Controllers;


use Illuminate\Http\Request;
use App\Services\WechatOfficial\Constant\UserEventType;

class AdminController extends BaseController
{
    public function reply(Request $request)
    {
        $type = $request->get('q');

        if(!isset(UserEventType::$replier_model[$type]))
        {
            return [["id"=>0,"text"=>"暂无"]];
        }

        $obj = (new UserEventType::$replier_model[$type]())->all();

        $response = [];
        foreach ($obj as $item) {
            $response[] = [
                "id" => $item['id'],
                "text" => $item['name']
            ];
        }

        return $response;
    }
}