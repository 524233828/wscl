<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019/1/18
 * Time: 14:35
 */

namespace App\Api\Logic;


use App\Api\Constant\ErrorCode;
use App\Models\WechatOfficialAccount;
use App\Models\WechatReceivedEvent;
use App\Models\WechatReceivedText;
use App\Models\WechatUserEvent;
use App\Services\WechatOfficial\Constant\UserEventType;
use App\Services\WechatOfficial\Receiver\MainReceiver;
use App\Services\WechatOfficial\WechatOfficialService;

class WechatLogic extends Logic
{

    public function getAccessToken($wx_app_id)
    {

        $sdk = new WechatOfficialService();
        $access_token = $sdk->getAccessToken($wx_app_id);

        if(!$access_token)
        {
            ErrorCode::error(ErrorCode::GET_ACCESS_TOKEN_FAIL);
        }

        return ["access_token" => $access_token];
    }

    public function index($original_id)
    {

        $wx_app_id = WechatOfficialAccount::where("original_id", $original_id)->first()->wx_app_id;

        $sdk = new WechatOfficialService();

        $response = $sdk->handleEvent($wx_app_id, function($message) use ($wx_app_id)
        {
            $msg_id = isset($message['MsgId']) ? $message['MsgId'] : md5(json_encode($message));

            $event = WechatUserEvent::where("msgid", $msg_id)->first();

            if(!empty($event))
            {

                return "";
            }
            $event = new WechatUserEvent();

            $event->setRawAttributes([
                "msgid" => $msg_id,
                "to_user_name" => $message['ToUserName'],
                "from_user_name" => $message['FromUserName'],
                "msg_type" => $message['MsgType'],
                "create_time" => date("Y-m-d H:i:s", $message['CreateTime']),
                "body" => json_encode($message)
            ]);

            $result = $event->save();
            $log = myLog("wechat_index");
            $log->addDebug("message",$message);
            $receive_handler = new MainReceiver();
            switch ($message['MsgType'])
            {
                case UserEventType::$alias[UserEventType::TEXT]:

                    $text = $message['Content'];
                    //文字回复只有全匹配和半匹配
                    //全匹配通过数据库直接查询
                    $receiver = WechatReceivedText::where(["wx_app_id" =>$wx_app_id,"type" => 1, "content" => $text])->first();
                    $log->addDebug("receiver".serialize($receiver));
                    if(!empty($receiver))
                    {
                        return $receive_handler->handle(
                            $receiver->id,
                            $wx_app_id,
                            $message['FromUserName'],
                            UserEventType::TEXT
                        );
                    }

                    //半匹配需要获取所有当前公众号的配置去匹配
                    $receivers = WechatReceivedText::where(["wx_app_id" =>$wx_app_id,"type" => 0])->get()->toArray();
                    $log->addDebug("receivers",$receivers);
                    foreach ($receivers as $receiver)
                    {
                        if(strpos($text,$receiver['content'])!==false)
                        {
                            return $receive_handler->handle(
                                $receiver['id'],
                                $wx_app_id,
                                $message['FromUserName'],
                                UserEventType::TEXT
                            );
                        }
                    }
                    break;
                case UserEventType::$alias[UserEventType::EVENT]:

                    $where['wx_app_id'] = $wx_app_id;
                    if(isset($message['Event'])){
                        $where['event'] = $message['Event'];
                    }

                    if(isset($message['EventKey'])){
                        $where['event_key'] = $message['EventKey'];
                    }

                    $receiver = WechatReceivedEvent::where($where)->first();

                    return $receive_handler->handle(
                        $receiver->id,
                        $wx_app_id,
                        $message['FromUserName'],
                        UserEventType::EVENT
                    );
                default:
                    return '';

            }
            

            return "";
        });

        return $response;
    }
}