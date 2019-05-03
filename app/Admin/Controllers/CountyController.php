<?php

/**
 * Created by JoseChan/Admin/ControllerCreator.
 * User: admin
 * DateTime: 2019-05-02 16:34:01
 */

namespace App\Admin\Controllers;

use App\Models\County;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class CountyController extends Controller
{

    use HasResourceActions;

    public function index()
    {
        return Admin::content(function (Content $content) {

            //页面描述
            $content->header('地区管理');
            //小标题
            $content->description('地区列表');

            //面包屑导航，需要获取上层所有分类，根分类固定
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '地区管理', 'url' => '/county']
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

            $content->header('地区管理');
            $content->description('编辑');

            //面包屑导航，需要获取上层所有分类，根分类固定
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '地区管理', 'url' => '/county'],
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

            $content->header('地区管理');
            $content->description('新增');

            //面包屑导航，需要获取上层所有分类，根分类固定
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '地区管理', 'url' => '/county'],
                ['text' => '新增']
            );

            $content->body($this->form());
        });
    }

    public function grid()
    {
        return Admin::grid(County::class, function (Grid $grid) {

            $grid->column("id","地区编码");
            $grid->column("name","地名");
            $grid->column("level","行政等级")->sortable();


            //允许筛选的项
            //筛选规则不允许用like，且搜索字段必须为索引字段
            //TODO: 使用模糊查询必须通过搜索引擎，此处请扩展搜索引擎
            $grid->filter(function (Grid\Filter $filter){

                $filter->where(function ($query) {
                    $query->where('name', 'like', "{$this->input}%");
                }, '地名');


            });


        });
    }

    protected function form()
    {
        return Admin::form(County::class, function (Form $form) {

            $form->display('id',"地区编码");
            $form->text('name',"地名")->rules("required|string");
            $form->datetime('created_at',"创建时间");
            $form->datetime('updated_at',"更新时间");
            $form->select("status","状态")->options([1=>"启用"]);

            $form->text('level',"行政等级")->rules("required|integer");


        });
    }
}