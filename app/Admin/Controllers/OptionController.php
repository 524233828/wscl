<?php

/**
 * Created by JoseChan/Admin/ControllerCreator.
 * User: admin
 * DateTime: 2019-05-01 21:49:50
 */

namespace App\Admin\Controllers;

use App\Models\Option;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class OptionController extends Controller
{

    use HasResourceActions;

    public function index()
    {
        return Admin::content(function (Content $content) {

            //页面描述
            $content->header('评分项选项管理');
            //小标题
            $content->description('评分项选项列表');

            //面包屑导航，需要获取上层所有分类，根分类固定
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '评分项选项管理', 'url' => '/options']
            );

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('评分项选项管理');
            $content->description('编辑');

            //面包屑导航，需要获取上层所有分类，根分类固定
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '评分项选项管理', 'url' => '/options'],
                ['text' => '编辑']
            );

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('评分项选项管理');
            $content->description('新增');

            //面包屑导航，需要获取上层所有分类，根分类固定
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '评分项选项管理', 'url' => '/options'],
                ['text' => '新增']
            );

            $content->body($this->form());
        });
    }

    public function grid()
    {
        return Admin::grid(Option::class, function (Grid $grid) {

            $grid->column("id","Id")->sortable();
            $grid->column("item_id","所属评分项")->sortable();
            $grid->column("name","选项");
            $grid->column("score","分值");


            //允许筛选的项
            //筛选规则不允许用like，且搜索字段必须为索引字段
            //TODO: 使用模糊查询必须通过搜索引擎，此处请扩展搜索引擎
            $grid->filter(function (Grid\Filter $filter){

                $filter->equal("item_id","所属评分项");


            });


        });
    }

    protected function form()
    {
        return Admin::form(Option::class, function (Form $form) {

            $form->display('id',"Id");
            $form->text('item_id',"所属评分项")->rules("required|integer");
            $form->text('name',"选项")->rules("required|string");
            $form->text('score',"分值")->rules("required|integer");
            $form->datetime('created_at',"创建时间");
            $form->datetime('updated_at',"更新时间");
            $form->select("status","状态")->options([0=>"冻结", 1=>"启动"]);



        });
    }
}