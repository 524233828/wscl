<?php

/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019/2/1
 * Time: 11:55
 */

namespace App\Services\WechatOfficial\Event\Handler;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Messages\Text;

class MessageReceivedHandler implements EventHandlerInterface
{

    public function handle($payload = null)
    {

        return new Text("您好");
    }
}