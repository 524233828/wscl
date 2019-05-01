<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019/1/25
 * Time: 23:36
 */

namespace App\Admin\Extensions\Tools;


use Encore\Admin\Grid\Tools\AbstractTool;

class WechatMenusReturn extends AbstractTool
{
    public function render()
    {
        $uri = url("/admin/wechat_official_accounts");

        return <<<EOT
<div class="btn-group" data-toggle="buttons">
    <a href="{$uri}" class="btn btn-sm btn-success">
        <i class="fa fa-arrow-left"></i>&nbsp;&nbsp;返回上级
    </a>
</div>
EOT;
    }
}