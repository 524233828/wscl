<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-04-28
 * Time: 17:09
 */

namespace App\Http\View;


use App\Cms\AbstractViewable;

class Container extends AbstractViewable
{

    /**
     * @var bool $is_leaf
     */
    protected $is_leaf = false;

    /**
     * @var string
     */
    protected $view = "web.container";

    /**
     * @var Container $instance
     */
    private static $instance;


    public function render()
    {
        return view($this->view, [
            "children" => $this->children
        ]);
    }

    /**
     * @return Container
     */
    public static function getInstance()
    {
        if(!(self::$instance instanceof Container))
        {
            self::$instance = new Container();
        }

        return self::$instance;
    }

    /**
     * @param callable $callback
     * @return Container
     */
    public function create(Callable $callback)
    {
        $container = self::getInstance();

        $callback($container);

        return $container;
    }

}