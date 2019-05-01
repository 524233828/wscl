<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019/2/4
 * Time: 23:31
 */

namespace App\Services\WechatOfficial\Constant;


use App\Models\WechatReceivedEventReply;
use App\Models\WechatReceivedReply;
use App\Models\WechatReplyText;
use App\Services\WechatOfficial\Replier\TextReplier;

class UserEventType
{
    const TEXT = 1;
    const IMAGE = 2;
    const VOICE = 3;
    const VIDEO = 4;
    const SHORT_VIDEO = 5;
    const LOCATION = 6;
    const LINK = 7;
    const EVENT = 8;

    public static $alias = [
        self::TEXT => "text",
        self::EVENT => "event",
    ];

    public static $replier_model = [
        self::TEXT => WechatReplyText::class,
    ];

    public static $replier = [
        self::TEXT => TextReplier::class,
    ];

    public static $connector_model = [
        self::TEXT => WechatReceivedReply::class,
        self::EVENT => WechatReceivedEventReply::class,
    ];
}