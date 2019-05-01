<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/12/4
 * Time: 18:24
 */

namespace App\Admin\Controllers;


use App\Admin\Models\Fields;
use App\Admin\Tools\ControllerCreator;
use App\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class DashBoardController extends Controller
{
    public function edit(Request $request, $key)
    {
        $form = Admin::form(Fields::class, function(Form $form) use ($key)
        {
            //基础设置部分
            $form->text("description", "页面描述");
            $form->text("title", "小标题");
            $form->text("route", "路由");
            $form->text("controller", "控制器名称");
            $form->text("model", "模型");

            //grid部分
            $fields = $form->model()->getAllFieldsFromTable($key);

            //为某些字段设置特殊格式
            $option = [];
            foreach($fields as $field)
            {
                $option[] = $field->Field;

                if("tinyint" === substr($field->Type, 0, 7))
                {
                    $form->text($field->Field, "设置{$field->Field}的枚举项");
                }
            }

            $form->multipleSelect("field", "列表显示项")->options($option);

            $form->multipleSelect("filter", "可供筛选项")->options($option);

            $form->multipleSelect("sortable", "可排序项")->options($option);

        });

        return Admin::content(function(Content $content) use($form)
        {
            $content->header("控制器生成");
            $content->description("新增控制器");

            $content->body($form);
        });

    }

    public function create(Request $request, $key)
    {

        $params = $request->request->all();

        $fields = (new Fields())->getAllFieldsFromTable($key);

        $creator = new ControllerCreator(
            new Filesystem(),
            $params,
            $fields
        );


        $result = $creator->create();

        $success = new MessageBag([
            'title'   => '操作结果',
            'message' => '操作成功',
        ]);

        return back()->with(compact('success'));

    }
}