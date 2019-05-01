<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/12/4
 * Time: 17:32
 */

namespace App\Admin\Extensions\Actions;


use Illuminate\Contracts\Support\Renderable;

class DashBoardCreate implements Renderable
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
        return <<<EOT
<a href="/admin/dash/{$this->key}/edit">
    <i class="fa fa-plus"></i>
</a>
EOT;
    }

    public function __toString()
    {
        return $this->render();
    }
}