<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019/2/8
 * Time: 19:19
 */

namespace App\Services\WechatOfficial\Replier;


use EasyWeChat\OfficialAccount\Application;
use Psr\Http\Message\ResponseInterface;
use EasyWeChat\Kernel\Support\Collection;

abstract class AbstractReplier
{

    /**
     * @param Application $app
     * @param array $params
     * @param string $to
     * @return ResponseInterface|Collection|array|object|string
     */
    abstract public function send(Application $app, array $params, $to);

    abstract public function getReplied(array $params);
}