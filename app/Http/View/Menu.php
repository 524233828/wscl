<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-04-28
 * Time: 18:33
 */

namespace App\Http\View;


 use App\Cms\AbstractViewable;

class Menu extends AbstractViewable
{

    /**
     * @var \App\Models\Menu;
     */
    protected $model;

    protected $view = "web.menu";

    protected $css = ["css/web/menu.css"];

    protected $is_leaf = true;

    public function __construct($model)
    {
        $this->model = $model;
    }

    protected function getData()
    {
        return $this->model::where(["status"=>1])->get()->all();
    }

    public function render()
    {
        // TODO: Implement render() method.
        return view($this->view, [
            "menus" => $this->getData()
        ]);
    }
}