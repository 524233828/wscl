<?php

/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019/2/4
 * Time: 16:37
 */
namespace App\Admin\Extensions\Form\Fields\Editors;

use Encore\Admin\Form\Field;

class WechatTextEditor extends Field
{
    protected $view = "admin::form.editor";

    protected static $js = [
        '//cdn.ckeditor.com/4.5.10/standard/ckeditor.js',
    ];

    public function render()
    {
        $this->script = <<<EOT
CKEDITOR.replace('{$this->column}',
{ 
    toolbar : [
        ['Link','Unlink'],
        ['Source']
    ],
    enterMode : CKEDITOR.ENTER_BR,
    shiftEnterMode : CKEDITOR.ENTER_P,
    forceEnterMode : false
});
EOT;


        return parent::render();
    }
}