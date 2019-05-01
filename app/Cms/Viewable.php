<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-04-28
 * Time: 17:13
 */

namespace App\Cms;


class Viewable extends AbstractViewable
{

    protected $data = [];

    protected $is_leaf = true;

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function render()
    {
        return view($this->view, $this->data);
    }

    public function view($view)
    {
        $this->view = $view;
        return $this;
    }
}