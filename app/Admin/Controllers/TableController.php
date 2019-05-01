<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/8/1
 * Time: 15:39
 */

namespace App\Admin\Controllers;


use App\Admin\Extensions\Actions\DashBoardCreate;
use App\Admin\Models\TableModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class TableController extends Controller
{

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('数据表');
            $content->description('列表');

            //面包屑导航，需要获取上层所有分类，根分类固定
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '数据表管理', 'url' => '/table']
            );


            $content->body($this->grid());
        });
    }

    public function grid()
    {
        return Admin::grid(TableModel::class, function (Grid $grid) {

            $grid->column("TABLE_NAME","表")->sortable();
            $grid->column("CREATE_TIME","创建时间")->sortable();
            $grid->column("TABLE_COMMENT","描述");

            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableCreateButton();

            $grid->actions(function(Grid\Displayers\Actions $actions){
                $actions->disableEdit();
                $actions->disableView();
                $actions->disableDelete();
                $actions->append(new DashBoardCreate($actions->getResource(), $actions->getKey()));
            });

        });
    }

    public function edit()
    {

    }

}