<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-04-29
 * Time: 19:41
 */

namespace App\Http\View;


use App\Cms\AbstractViewable;

class Content extends AbstractViewable
{

    /**
     * @var bool $is_leaf
     */
    protected $is_leaf = false;

    /**
     * @var string
     */
    protected $view = "web.content";

    protected $css = ["css/web/content_base.css",];

    /**
     * @var Content $instance
     */
    private static $instance;


    public function render()
    {
        return view($this->view, [
            "children" => $this->children
        ]);
    }

    /**
     * @return Content
     */
    public static function getInstance()
    {
        if(!(self::$instance instanceof Content))
        {
            self::$instance = new Content();
        }

        return self::$instance;
    }

    /**
     * @param callable $callback
     * @return Content
     */
    public function create(Callable $callback)
    {
        $container = self::getInstance();

        $callback($container);

        return $container;
    }

}