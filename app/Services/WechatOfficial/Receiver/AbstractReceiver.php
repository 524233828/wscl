<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019/2/9
 * Time: 8:31
 */

namespace App\Services\WechatOfficial\Receiver;


use App\Services\WechatOfficial\Constant\UserEventType;
use App\Services\WechatOfficial\WechatOfficialService;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractReceiver
{

    public function handle($received_id, $wx_app_id, $openid, $type)
    {
        
        if(isset(UserEventType::$connector_model[$type])){
            $class = UserEventType::$connector_model[$type];
            /**
             * @var Model $obj
             */
            $obj = new $class();
        }else{
            return "";
        }
        //获取所有响应者
        $received_reply = $obj->where("received_id", $received_id)->get()->toArray();

        //根据类型分类
        $type_index_reply_id = [];
        //类型-响应者ID=>对象数组
        $type_reply_id_index = [];
        foreach($received_reply as $value)
        {
            $type_index_reply_id[$value['type']][] = $value['reply_id'];
            $type_reply_id_index[$value['type']][$value['reply_id']] = $value;
        }

        //获取所有响应者
        $replier = [];
        foreach ($type_index_reply_id as $type => $ids)
        {
            if(isset(UserEventType::$replier_model[$type]))
            {

                $class = UserEventType::$replier_model[$type];
                /**
                 * @var Model $model
                 */
                $model = new $class();
                $reply = $model->whereIn("id", $ids)->get()->all();

                foreach ($reply as &$item)
                {
                    $item['type'] = $type_reply_id_index[$type][$item['id']]['type'];
                    $item['sort'] = $type_reply_id_index[$type][$item['id']]['sort'];
                }

                $replier = array_merge($replier, $reply);

            }
        }

        //排序
        $replier = array_values(collect($replier)->sortByDesc("sort")->all());

        //弹出最后一个消息用于被动回复
        $been_replied = array_pop($replier);

        //发送
        $sdk = new WechatOfficialService();
//        foreach ($replier as $item)
//        {
//            $result = $sdk->sendCustom($wx_app_id, $item['type'], $item->toArray(), $openid);
//        }

        return $sdk->getReplied($been_replied['type'], $been_replied->toArray());
    }
}