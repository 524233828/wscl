<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-04-28
 * Time: 15:57
 */

namespace App\Http\View;


use App\Cms\AbstractViewable;
use phpDocumentor\Reflection\Types\Callable_;

class Cms extends AbstractViewable
{

    /**
     * @var bool $is_leaf
     */
    protected $is_leaf = true;

    /**
     * @var string
     */
    protected $view = "web.framework";

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var AbstractViewable $container
     */
    private $container;

    /**
     * @var Cms
     */
    private static $instance;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function render()
    {

        if(empty($this->container)){
            $this->container = Container::getInstance();
        }

        return view($this->view, [
            "title" => $this->title,
            "js" => array_merge($this->getJs(), $this->container->getChildrenJs()),
            "css" => array_merge($this->getCss(), $this->container->getChildrenCss()),
            "container" => $this->container->render()
        ]);
    }

    /**
     * @return Cms
     */
    public static function getInstance()
    {
        if(!(self::$instance instanceof Cms))
        {
            self::$instance = new Cms();
        }

        return self::$instance;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function title($title = ""){
        $this->title = $title;

        return $this;
    }

    /**
     * @param callable $callback
     * @return Cms
     */
    public function create(Callable $callback)
    {
        $cms = self::getInstance();

        $callback($cms);

        return $cms;
    }

    public function container(Callable $callback)
    {
        $container = Container::getInstance();

        $container = $container->create($callback);

        return $container;
    }

    public function menu($model)
    {
        return new Menu($model);
    }

    public function header()
    {
        return new Header($this->title);
    }

    public function banner($model){
        return new Banner($model);
    }

    public function content()
    {
        return new Content();
    }

}