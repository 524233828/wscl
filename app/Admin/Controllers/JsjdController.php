<?php

/**
 * Created by JoseChan/Admin/ControllerCreator.
 * User: admin
 * DateTime: 2019-05-04 20:36:44
 */

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\Export;
use App\Api\Constant\Score;
use App\Models\BuildInfo;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\County;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Database\Eloquent\Builder;

class JsjdController extends Controller
{

    use HasResourceActions;

    public function index()
    {
        return Admin::content(function (Content $content) {

            //页面描述
            $content->header('评分信息管理');
            //小标题
            $content->description('评分信息列表');

            //面包屑导航，需要获取上层所有分类，根分类固定
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '评分信息管理', 'url' => '/jsjds']
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

            $content->header('评分信息管理');
            $content->description('编辑');

            //面包屑导航，需要获取上层所有分类，根分类固定
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '评分信息管理', 'url' => '/jsjds'],
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

            $content->header('评分信息管理');
            $content->description('新增');

            //面包屑导航，需要获取上层所有分类，根分类固定
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '评分信息管理', 'url' => '/jsjds'],
                ['text' => '新增']
            );

            $content->body($this->form());
        });
    }

    public function grid()
    {
        return Admin::grid(BuildInfo::class, function (Grid $grid) {

            $county = County::getCounty();
            $grid->column("id","ID");
            $grid->column("company.county","县（市、区）")->display(function ($value) use ($county){
                return $county[$value];
            })->sortable();
            $grid->column("company.name","污水厂")->sortable();
            $grid->column("score","分数")->sortable();
            $grid->column("month","报告月份")->sortable();
            $grid->column("created_at","提交时间")->sortable();


            $grid->tools(function (Grid\Tools $tools) {
                $tools->append(new Export());
            });
            $grid->disableExport();
            //允许筛选的项
            //筛选规则不允许用like，且搜索字段必须为索引字段
            //TODO: 使用模糊查询必须通过搜索引擎，此处请扩展搜索引擎
            $grid->filter(function (Grid\Filter $filter) use ($county){

                $company = Company::getCompany();
                $filter->equal("company_id","污水厂")->select($company);
                $filter->equal("company.county","县（市、区）")->select($county);
                $filter->where(function (Builder $query){
                    $query->where("month", "=", $this->input);
                },"提交的月份","month")->datetime(['format' => 'YYYYMM']);
            });


        });
    }

    protected function form()
    {
        return Admin::form(BuildInfo::class, function (Form $form) {

            $companies = Company::getCompany();
            $form->display('id',"ID");
            $form->select('company_id',"污水厂")->options($companies);
            $form->text('tzms',"投资模式");
            $form->text('sgdw',"施工单位");
            $form->text('sgfzr',"施工负责人");
            $form->text('zw',"职务");
            $form->text('lxfs',"联系方式");
            $form->text('sgxclxr',"施工现场联系人");
            $form->text('xclxrlxfs',"现场联系人联系方式");
            $form->select("xz","选址")->options([0 => "未完成（0分）", 1=>"已完成（10分）"]);

            $form->select("zd","征地")->options([0 => "未完成（0分）", 1=>"已完成（20分）"]);

            $form->select("styp","三通一平")->options([0 => "未开始（0分）", 1=>"已开始（5分）", 2=>"已完成（10分）"]);

            $form->select("kt","勘探")->options([0 => "未完成（0分）", 1=>"已完成（10分）"]);

            $form->select("gwsg","管网施工")->options([0 => "未开始（0分）", 1=>"已开始（5分）", 2=>"已完成（10分）"]);

            $form->select("tjsg","土建施工")->options([0 => "未开始（0分）", 1=>"已开始（10分）", 2=>"已完成（15分）"]);

            $form->select("jdaz","机电安装")->options([0 => "未开始（0分）", 1=>"已开始（5分）", 2=>"已完成（10分）"]);

            $form->select("syx","试运行")->options([0 => "未完成（0分）", 1=>"已完成（10分）"]);

            $form->select("zsyx","正式运行")->options([0 => "未完成（0分）", 1=>"已完成（5分）"]);

            $form->select("jsjd","建设进度")->options([
                0 => "选址、征地、三通一平、勘探等指标比上月无进展（-10分）",
                1=>"管网施工比上月无进展（-5分）",
                2=>"当月已完成三通一平和勘探，下月未开始土建施工（-5分）",
                3=>"出现其他停滞情况（-5分）",
                4=>"无扣分项（0分）",
                5=>"其他扣分（-15分）",
                6=>"其他扣分（-20分）",
                7=>"其他扣分（-25分）",
                8=>"其他扣分（-30分）",
                9=>"其他扣分（-35分）",
            ])->default(4);

            $form->editor('czwt', '存在问题');
            $form->datetime('created_at',"提交时间");
            $form->datetime('updated_at',"最近更新时间");
            $form->select("status","状态")->options([0 => "冻结", 1=>"启用"])->default(1);

            $form->text('score',"score")->default(0)->readOnly();
            $form->datetime('month',"提交的月份")->format("YYYYMM")->rules("required|string");


            $form->saving(function (Form $form) {
                $form->model()->created_time = time();
                /**
                 * attention! if we have not set "__isset" function in the class,
                 * php cannot judge if the attribute existed in the object, or if they are empty.
                 * empty($form->czwt) -- true
                 */
                $czwt = $form->czwt;
                $form->czwt = empty($czwt) ? "暂无" : strip_tags($czwt);

            });

            $form->saved(function (Form $form) {

                $data = $form->model()->toArray();
                $score = Score::computer($data);
                $form->model()->score = $score;
                $form->model()->save();
                /**
                 * 获取最后一个月的评分项
                 */
                $last_month_data = BuildInfo::where([
                    ["company_id","=",$form->model()->company_id],
                ])->orderByDesc("month")->limit(1)->get()->toArray();
                $last_month_data = $last_month_data[0];
                if($last_month_data['id'] == $data['id'])
                {
                    $company = Company::find($form->model()->company_id);
                    $company->score = Score::computer($last_month_data);
                    $company->save();
                }
            });

        });
    }
}