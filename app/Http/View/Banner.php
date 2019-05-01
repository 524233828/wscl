<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-04-28
 * Time: 18:33
 */

namespace App\Http\View;


 use App\Cms\AbstractViewable;

class Banner extends AbstractViewable
{

    /**
     * @var \App\Models\Menu;
     */
    protected $model;

    protected $view = "web.banner";

    protected $css = ["css/web/banner.css"];

    protected $js = ["/js/jquery.SuperSlide.2.1.3.js"];

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
            "banners" => $this->getData()
        ]);
    }

    public function script()
    {
        return <<<SCRIPT
    jQuery(".slideBox").slide( { mainCell:".bd ul",effect:"left", trigger: "click", autoPlay: true, interTime: 4000});
SCRIPT;

    }
}