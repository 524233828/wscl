<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-03-26
 * Time: 12:00
 */

namespace App\Admin\Extensions\Tools;


use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;

class FetchBaiduSites extends AbstractTool
{

    protected function script()
    {
        $uri = url("/admin/bdtj_sites/fetch");

        return <<<EOT

$('#site_fetch').click(function () {

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
                    toastr.success('拉取成功！');
                } else {
                    toastr.fail('拉取失败！');
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
    <div class="btn btn-sm btn-success" id="site_fetch">
        <i class="fa fa-arrow-left"></i>&nbsp;&nbsp;拉取站点
    </div>
</div>
EOT;
    }
}