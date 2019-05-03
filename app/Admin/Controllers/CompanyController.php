<?php

/**
 * Created by JoseChan/Admin/ControllerCreator.
 * User: admin
 * DateTime: 2019-05-01 21:36:48
 */

namespace App\Admin\Controllers;

use App\Models\Company;
use App\Http\Controllers\Controller;
use App\Models\County;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class CompanyController extends Controller
{

    use HasResourceActions;

    public function index()
    {
        return Admin::content(function (Content $content) {

            //页面描述
            $content->header('污水厂管理');
            //小标题
            $content->description('污水厂列表');

            //面包屑导航，需要获取上层所有分类，根分类固定
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '污水厂管理', 'url' => '/companies']
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

            $content->header('污水厂管理');
            $content->description('编辑');

            //面包屑导航，需要获取上层所有分类，根分类固定
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '污水厂管理', 'url' => '/companies'],
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

            $content->header('污水厂管理');
            $content->description('新增');

            //面包屑导航，需要获取上层所有分类，根分类固定
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '污水厂管理', 'url' => '/companies'],
                ['text' => '新增']
            );

            $content->body($this->form());
        });
    }

    public function grid()
    {
        return Admin::grid(Company::class, function (Grid $grid) {

            $grid->column("id","ID");
            $grid->column("name","污水厂");
            $grid->column("completed_at","建成时间")->sortable();
            $grid->column("address","详细地址");
            $grid->column("authority","主管单位");
            $grid->column("leader","分管领导");
            $grid->column("contact","联系方式");
            $grid->column("status","状态")->using([0=>"未建成", 1=>"已建成"]);


            //允许筛选的项
            //筛选规则不允许用like，且搜索字段必须为索引字段
            //TODO: 使用模糊查询必须通过搜索引擎，此处请扩展搜索引擎
            $grid->filter(function (Grid\Filter $filter){

                $filter->equal("status","状态")->select([0=>"未建成", 1=>"已建成"]);



            });


        });
    }

    protected function form()
    {
        return Admin::form(Company::class, function (Form $form) {

            $county = $this->getCounty();
            $form->display('id',"ID");
            $form->text('name',"污水厂")->rules("required|string");
            $form->select('county',"区/县")->options($county);
            $form->text('scale',"规模")->default("");
            $form->text('operation_mode',"运营模式")->default("");
            $form->datetime('completed_at',"建成时间")->default("");
            $form->text('tecnology',"工艺")->default("");
            $form->text('water_quality',"出水标准")->default("");
            $form->text('pipeline_length',"现状网管长度")->default("");
            $form->text('address',"详细地址")->default("");
            $form->text('authority',"主管单位")->default("");
            $form->text('leader',"分管领导")->default("");
            $form->text('job',"职务")->default("");
            $form->text('contact',"联系方式")->default("");
            $form->select("status","状态")->options([0=>"未建成", 1=>"已建成"]);



        });
    }

    protected function getCounty()
    {
        $county = County::all(["id","name"])->toArray();
        $county_index_arr = [];
        foreach ($county as $value){
            $county_index_arr[$value['id']] = $value['name'];
        }
        return $county_index_arr;
    }
}