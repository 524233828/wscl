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

class WechatReceivedText implements Renderable
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
        $uri = url("/admin/app/{$this->key}/wechat_received_texts");

        return <<<EOT
<a href="{$uri}" title="文本接收处理器">
    <i class="fa fa-file-text-o"></i>
</a>
EOT;
    }

    public function __toString()
    {
        return $this->render();
    }
}