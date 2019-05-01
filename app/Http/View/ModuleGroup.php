<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-04-30
 * Time: 10:48
 */

namespace App\Http\View;


use App\Cms\AbstractViewable;

class ModuleGroup extends AbstractViewable
{
    /**
     * @var bool $is_leaf
     */
    protected $is_leaf = false;

    /**
     * @var string
     */
    protected $view = "web.module-group";

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
     * @return ModuleGroup
     */
    public static function getInstance()
    {
        if(!(self::$instance instanceof ModuleGroup))
        {
            self::$instance = new ModuleGroup();
        }

        return self::$instance;
    }

    /**
     * @param callable $callback
     * @return ModuleGroup
     */
    public function create(Callable $callback)
    {
        $container = self::getInstance();

        $callback($container);

        return $container;
    }

}