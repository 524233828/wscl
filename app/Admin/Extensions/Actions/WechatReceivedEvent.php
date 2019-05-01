<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019/1/25
 * Time: 22:22
 */

namespace App\Admin\Extensions\Actions;


use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Route;

class WechatReceivedEvent implements Renderable
{
    protected $resource;
    protected $key;

    public function __construct($resource, $key)
    {
        $this->resource = $resource;
        $this->key = $key;
    }

    public function render()
    {
        $uri = url("/admin/app/{$this->key}/wechat_received_events");

        return <<<EOT
<a href="{$uri}" title="事件接收处理器">
    <i class="fa fa-clipboard"></i>
</a>
EOT;
    }

    public function __toString()
    {
        return $this->render();
    }
}