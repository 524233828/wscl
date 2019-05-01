<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-04-28
 * Time: 15:36
 */

namespace App\Cms;


use Illuminate\Contracts\Support\Renderable;

abstract class AbstractViewable implements Renderable
{

    protected $js = [];

    protected $css = [];

    /**
     * @var array<AbstractViewable> $children
     */
    protected $children = [];

    /**
     * @var bool $is_leaf
     */
    protected $is_leaf;

    protected $view;

    /**
     * @param array $css
     * @return $this
     */
    public function setCss(array $css): AbstractViewable
    {
        $this->css = $css;
        return $this;
    }

    /**
     * @return array
     */
    public function getCss(): array
    {
        return $this->css;
    }

    /**
     * @param $css
     * @return $this
     */
    public function appendCss($css)
    {
        array_push($this->css, $css);
        return $this;
    }
    /**
     * @return array
     */
    public function getJs(): array
    {
        return $this->js;
    }

    /**
     * @param array $js
     * @return $this
     */
    public function setJs(array $js): AbstractViewable
    {
        $this->js = $js;

        return $this;
    }

    /**
     * @param string $js
     * @return $this
     */
    public function addJs(string $js)
    {
        array_push($this->js, $js);
        return $this;
    }

    /**
     * @param array $js
     * @return $this
     */
    public function appendJs(array $js)
    {
        $this->js = array_merge($this->js, $js);
        return $this;
    }

    /**
     * @param AbstractViewable $child
     * @return $this
     */
    public function addChild(AbstractViewable $child): AbstractViewable
    {
        if($this->is_leaf){
            return $this;
        }

        $this->children[] = $child;

        return $this;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @return array
     */
    protected function getChildrenCss()
    {

        $css = [];
        if(!$this->is_leaf){

            /**
             * @var AbstractViewable $child;
             */
            foreach ($this->children as $child){

                 $css = array_merge($css, $this->getCss(), $child->getChildrenCss());

            }
        }else{
            return $this->getCss();
        }

        return $css;
    }

    /**
     * @return array
     */
    protected function getChildrenJs()
    {

        $js = [];
        if(!$this->is_leaf){

            /**
             * @var AbstractViewable $child;
             */
            foreach ($this->children as $child){

                $js = array_merge($js, $this->getJs(), $child->getChildrenJs());

            }
        }else{
            return $this->getJs();
        }

        return $js;
    }

}