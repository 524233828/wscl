<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019/1/25
 * Time: 23:36
 */

namespace App\Admin\Extensions\Tools;


use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;

class WechatMenusCreate extends AbstractTool
{
    protected $wx_app_id;

    public function __construct($wx_app_id)
    {
        $this->wx_app_id = $wx_app_id;
    }

    protected function script()
    {
        $uri = url("/admin/wechat_menu/create/{$this->wx_app_id}");

        return <<<EOT

$('#menu_create').click(function () {

    var url = "$uri";

    $.ajax({
        method: 'post',
        url: url,
        data: {
            _token:LA.token,
        },
        success: function (data) {
            $.pjax.reload('#pjax-container');

            if (typeof data === 'object') {
                if (data.status) {
                    toastr.success('创建成功！');
                } else {
                    toastr.fail('创建失败！');
                }
            }
        }
    });
});

EOT;
    }

    public function render()
    {

        Admin::script($this->script());

        return <<<EOT
<div class="btn-group" data-toggle="buttons">
    <div class="btn btn-sm btn-success" id="menu_create">
        <i class="fa fa-arrow-left"></i>&nbsp;&nbsp;创建菜单
    </div>
</div>
EOT;
    }
}