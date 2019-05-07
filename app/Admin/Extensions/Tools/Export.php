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

class Export extends AbstractTool
{

    protected function script()
    {
        $uri = url("/api/export");

        return <<<EOT

$('#site_fetch').click(function () {

    var month = document.getElementById("month").value;
    if(month == ""){
        alert("请选择导出的月份");
        return false;
    }
    var url = "$uri?month=" + month;
    location.href = url;
});

EOT;
    }

    public function render()
    {

        Admin::script($this->script());

        return <<<EOT
<div class="btn-group" data-toggle="buttons">
    <div class="btn btn-sm btn-success" id="site_fetch">
        <i class="fa fa-download"></i>&nbsp;&nbsp;导出
    </div>
</div>
EOT;
    }
}