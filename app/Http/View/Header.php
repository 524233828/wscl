<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-04-29
 * Time: 16:56
 */

namespace App\Http\View;


use App\Cms\AbstractViewable;

class Header extends AbstractViewable
{
    protected $view = "web.header";

    protected $css = ["css/web/header.css"];

    protected $is_leaf = true;

    protected $title = "";

    public function __construct($title)
    {
        $this->title=$title;
    }

    public function render()
    {
        return view($this->view, ["title" => $this->title]);
    }
}