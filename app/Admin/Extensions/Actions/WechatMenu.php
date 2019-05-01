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

class WechatMenu implements Renderable
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
        $uri = url("/admin/wx_app_id/{$this->key}/wechat_menus");

        return <<<EOT
<a href="{$uri}" title="自定义菜单">
    <i class="fa fa-bars"></i>
</a>
EOT;
    }

    public function __toString()
    {
        return $this->render();
    }
}