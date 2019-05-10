<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/11/21
 * Time: 15:02
 */

namespace App\Api\Controllers;


use App\AdminUser;
use App\Api\Constant\ErrorCode;
use App\Api\Constant\Score;
use App\Console\Commands\PhoneArea;
use App\Constant\JWTKey;
use App\Models\BuildInfo;
use App\Models\Company;
use App\Models\County;
use App\Models\ScoreItem;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class IndexController extends BaseController
{

    public function login(Request $request)
    {
        $credentials = $request->only(['username', 'password']);

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($credentials, [
            'username'          => 'required',
            'password'          => 'required',
        ]);

        if ($validator->fails()) {//登录失败
            return \response()->json(["code" => 1000, "msg"=>"登录失败", "data"=>[]]);
        }

        if (Auth::guard('admin')->attempt($credentials)) {//登录成功

            $admin = AdminUser::where("username", "=", $request->get("username"))->get(["id"])->toArray();

            return \response()->json(["code" => 1, "msg"=>"登录成功", "data"=>["token"=>$this->generateJWT($admin[0]['id'])]]);
        }

        return \response()->json(["code" => 1000, "msg"=>"登录失败", "data"=>[]]);
    }

    protected function generateJWT($uid)
    {
        $token = [
            'iss' => JWTKey::ISS,
            'aud' => (string)$uid,
            'iat' => time(),
            'exp' => time() + (3600 * 24 * 365), // 有效期一年
        ];

        return JWT::encode($token, JWTKey::KEY, JWTKey::ALG);
    }

    public function getCounty(Request $request)
    {
        $city_id = $request->get("city_id", "440200");

        $county = County::where("city_id", "=", $city_id)->get(["id", "name"])->toArray();

        $company = new Company();


        $score = $company->getCountyAverageScore();

        $score_arr = [];
        foreach ($score as $value){
            $score_arr[$value->county] = floor($value->score);
        }

        foreach ($county as &$item){
            if(isset($score_arr[$item['id']])){
                $item["score"] = $score_arr[$item['id']];
            }else{
                $item['score'] = 0;
            }
        }

        return $this->response($county);
    }

    public function getCompanies(Request $request)
    {
        $county_id = $request->get("county_id");

        $where = [];

        if(!empty($county_id)) {
            $where["county"] = $county_id;
            $county = County::find($county_id)->toArray();
            $county_name = $county['name'];

        }else{
            $county_name = "韶关市";
        }

        $companies = Company::where($where)->get(["id", "name","score","status"]);

        if(!empty($companies)){
            $companies = $companies->toArray();
        }else{
            $companies = [];
        }

        $data = ["county_name" => $county_name, "companies" => $companies];

        return $this->response($data);
    }

    public function getCompanyInfo(Request $request)
    {
        $company_id = $request->get("company_id");

        if(empty($company_id)){
            return $this->response(
                [],
                ErrorCode::msg(ErrorCode::COMPANY_NOT_FOUND),
                ErrorCode::COMPANY_NOT_FOUND
            );
        }

        $company = Company::find($company_id);

        if(empty($company)){
            return $this->response(
                [],
                ErrorCode::msg(ErrorCode::COMPANY_NOT_FOUND),
                ErrorCode::COMPANY_NOT_FOUND
            );
        }

        $base_info = $company->toArray();

        $build_info = [];
        //获取最近一次填写的建设信息
        if($base_info['status']==0)
        {
            $build = BuildInfo::where("company_id", "=", $company_id)
                ->orderBy("created_time","desc")->first();

            if(!empty($build)){
                $build_info = $build->toArray();
            }
        }

        return $this->response(["base_info"=>$base_info, "build_info"=>$build_info]);
    }

    public function updateBuildInfo(Request $request)
    {
        $data = $request->toArray();

        if(!isset($data['czwt'])){
            $data['czwt'] = "";
        }

        $validator = validator($data, [
            "tzms" => "required",
            "sgdw" => "required",
            "sgfzr" => "required",
            "zw" => "required",
            "lxfs" => "required",
            "sgxclxr" => "required",
            "xclxrlxfs" => "required",
            "company_id" => "required",
            "xz" => "required|in:0,1",
            "zd" => "required|in:0,1",
            "styp" => "required|in:0,1,2",
            "kt" => "required|in:0,1",
            "gwsg" => "required|in:0,1,2",
            "tjsg" => "required|in:0,1,2",
            "jdaz" => "required|in:0,1,2",
            "syx" => "required|in:0,1",
            "zsyx" => "required|in:0,1",
            "jsjd" => "required|in:0,1,2,3",
            "month" => "required",
        ]);

        if($validator->fails()){
            return $this->response(
                [],
                ErrorCode::msg(ErrorCode::PARAMS_ERROR),
                ErrorCode::PARAMS_ERROR
            );
        }

        //管理员地区获取及判断
        $admin = AdminUser::$user->toArray()[0];
        $admin = ["address" => $admin['address_id']];

        $company = Company::find($data['company_id']);
        if(empty($company)){
            return $this->response(
                [],
                ErrorCode::msg(ErrorCode::COMPANY_NOT_FOUND),
                ErrorCode::COMPANY_NOT_FOUND
            );
        }

        $company_arr = $company->toArray();

        if($admin["address"] != 440200 && $admin["address"] != $company_arr['county']){
            return $this->response(
                [],
                ErrorCode::msg(ErrorCode::FORBIDDEN),
                ErrorCode::FORBIDDEN
            );
        }

        $data['score'] = Score::computer($data);
        $data['created_time'] = time();

        $company->score = $data['score'];
        $company->save();

        $score = new BuildInfo();
        $score_find = $score->where([["company_id", "=", $data['company_id']], ["month", "=", $data['month']]])->first();

        if($score_find){
            $result = $score->where([["company_id", "=", $data['company_id']], ["month", "=", $data['month']]])->update($data);
        }else{
            $score->setRawAttributes($data);
            $result = $score->save();
        }

        if($result){
            return $this->response([]);
        }else{
            return $this->response(
                [],
                ErrorCode::msg(ErrorCode::SYSTEM_ERROR),
                ErrorCode::SYSTEM_ERROR
            );
        }
    }

    public function excel(Request $request)
    {

        $month = $request->get("month");
        $time = strtotime($month."01000000");
        $year = date("Y", $time);
        $months = date("m", $time);
        if(empty($month)){
            return false;
        }

        /**
         * data格式
         *
         * [
         *  county_id => [
         *      "county" => ""
         *      "sum_score" => ""
         *      "score" => ""
         *      "finish_rate" => ""
         *      "rank" => "",
         *      "companies" => [
         *          company_id => [
         *              "name" => ""
         *              "各项评分" => ""
         *          ]
         *      ]
         *  ]
         * ]
         */
        $data = [];

        //获取市县
        $counties = County::all(["id", "name"])->toArray();

        foreach($counties as $county)
        {
            $data[$county['id']] = [
                "county" => $county['name'],
                "sum_score" => 0,
                "total_score" => 0,
                "finish_rate" => 0,
                "rank" => 0,
                "companies" => [],
            ];
        }

        //获取污水厂
        $companies = Company::all(["id", "name", "county"])->toArray();

        foreach ($companies as $company){
            $data[$company["county"]]["companies"][$company['id']] = [
                "name" => $company['name'],
                "xz" => 0,
                "zd" => 0,
                "styp" => 0,
                "kt" => 0,
                "gwsg" => 0,
                "tjsg" => 0,
                "jdaz" => 0,
                "syx" => 0,
                "zsyx" => 0,
                "jsjd" => 0,
                "score" => 0,
            ];
        }

        //获取月份数据
        $build_info = BuildInfo::where("month","=" , $month)
            ->leftJoin("wscl_companies","wscl_jsjd.company_id", "=", "wscl_companies.id")->get();
        $score = [];

        foreach ($build_info as $item)
        {
            $item = $item->toArray();
            if(isset($score[$item['county']])){
                $score[$item['county']] += Score::computer($item);
            }else{
                $score[$item['county']] = Score::computer($item);
            }

            $data[$item['county']]["companies"][$item['company_id']]["xz"] = Score::$score_list["xz"][$item['xz']];
            $data[$item['county']]["companies"][$item['company_id']]["zd"] = Score::$score_list["zd"][$item['zd']];
            $data[$item['county']]["companies"][$item['company_id']]["styp"] = Score::$score_list["styp"][$item['styp']];
            $data[$item['county']]["companies"][$item['company_id']]["kt"] = Score::$score_list["kt"][$item['kt']];
            $data[$item['county']]["companies"][$item['company_id']]["gwsg"] = Score::$score_list["gwsg"][$item['gwsg']];
            $data[$item['county']]["companies"][$item['company_id']]["tjsg"] = Score::$score_list["tjsg"][$item['tjsg']];
            $data[$item['county']]["companies"][$item['company_id']]["jdaz"] = Score::$score_list["jdaz"][$item['jdaz']];
            $data[$item['county']]["companies"][$item['company_id']]["syx"] = Score::$score_list["syx"][$item['syx']];
            $data[$item['county']]["companies"][$item['company_id']]["zsyx"] = Score::$score_list["zsyx"][$item['zsyx']];
            $data[$item['county']]["companies"][$item['company_id']]["jsjd"] = Score::$score_list["jsjd"][$item['jsjd']];
            $data[$item['county']]["companies"][$item['company_id']]["score"] = Score::computer($item);

        }



        foreach ($data as $key => &$datum){
            if(isset($score[$key])){
                $datum["sum_score"] = $score[$key];
            } else{
                $datum["sum_score"] = 0;
            }
            $count = count($datum["companies"]);
            $datum["total_score"] = $count * 100;

            if($count != 0 ){
                $datum["finish_rate"] = (float)bcdiv($datum["sum_score"], $count, 2);
            }else{
                $datum["finish_rate"] = 0;
            }

            $data[$key]["companies"] = array_values($data[$key]["companies"]);
        }

        $data = array_values($data);
//        var_dump($data);exit;

        $stored = collect($data)->sortByDesc(function($item, $key){
            return $item['finish_rate'];
        });

        $data =$stored->values()->all();
        $spreadsheet = new Spreadsheet();
        $title = "{$year}年{$months}月份镇级污水处理设施及配套管网建设工作考核得分表";
        try{
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->mergeCells("A1:T1");
            $sheet->setCellValue("A1",$title);
            $sheet->mergeCells("A2:T2");
            //表头
            $head = ["序号","县（市、区）","项目名称","管网施工（10分）","选址（10分）","征地（20分）","三通一平（10分）","勘探（10分）",
                "土建施工（15分）","机电安装（10分）","试运行（10分）","正式运行（5分）","建设进度（-10分）","得分","得分合计","总分",
                "分值占比","完成率","排名","扣分说明",];

            $sheet->fromArray($head, null, "A3");

            $sheet->freezePane("A1");
            $sheet->freezePane("A2");
            $sheet->freezePane("A3");
            $sheet->getRowDimension("1")->setRowHeight(35);
            $sheet->getRowDimension("2")->setRowHeight(35);
            $sheet->getRowDimension("3")->setRowHeight(68);
            $sheet->getStyle("A1")->applyFromArray(
                [
                    "font" => ["size" => 18],
                    "alignment" => ["horizontal" => Alignment::HORIZONTAL_CENTER]
                ]
            );

            $i = 1;
            $k = 1;//序号
            $row_index = 4;
            foreach ($data as &$datum)
            {
                $datum["rank"] = $i;
                $i++;

                $count = count($datum['companies']);
                if($count == 0){
                    $to_row_index =$row_index;
                }else{
                    $to_row_index =$row_index + $count - 1;
                }


                $sheet->mergeCells("B".$row_index.":B".$to_row_index);
                $sheet->mergeCells("O".$row_index.":O".$to_row_index);
                $sheet->mergeCells("P".$row_index.":P".$to_row_index);
                $sheet->mergeCells("Q".$row_index.":Q".$to_row_index);
                $sheet->mergeCells("R".$row_index.":R".$to_row_index);
                $sheet->mergeCells("S".$row_index.":S".$to_row_index);

                $sheet->setCellValue("B".$row_index, $datum['county']. "({$count}座)");
                $sheet->setCellValue("O".$row_index, $datum['sum_score']);
                $sheet->setCellValue("P".$row_index, $datum['total_score']);
                $sheet->setCellValue("Q".$row_index, $datum['finish_rate']);
                $sheet->setCellValue("R".$row_index, $datum['finish_rate']. "%");
                $sheet->setCellValue("S".$row_index, $datum['rank']);

                $j = $row_index;//行数

                foreach ($datum['companies'] as $company)
                {
                    $sheet->setCellValue("A".$j, $k);
                    $sheet->setCellValue("C".$j, $company['name']);
                    $sheet->setCellValue("D".$j, $company['gwsg']);
                    $sheet->setCellValue("E".$j, $company['xz']);
                    $sheet->setCellValue("F".$j, $company['zd']);
                    $sheet->setCellValue("G".$j, $company['styp']);
                    $sheet->setCellValue("H".$j, $company['kt']);
                    $sheet->setCellValue("I".$j, $company['tjsg']);
                    $sheet->setCellValue("J".$j, $company['jdaz']);
                    $sheet->setCellValue("K".$j, $company['syx']);
                    $sheet->setCellValue("L".$j, $company['zsyx']);
                    $sheet->setCellValue("M".$j, $company['jsjd']);
                    $sheet->setCellValue("N".$j, $company['score']);
                    $j++;
                    $k++;
                }

                $row_index = $to_row_index+1;

            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
        }catch (\Exception $exception){
            echo $exception->getMessage();
        }

    }

    public function updateBaseInfo(Request $request)
    {
        $data = $request->toArray();

        if(!isset($data['czwt'])){
            $data['czwt'] = "";
        }

        $validator = validator($data, [
            "company_id" => "required",
        ]);

        if($validator->fails()){
            return $this->response(
                [],
                ErrorCode::msg(ErrorCode::PARAMS_ERROR),
                ErrorCode::PARAMS_ERROR
            );
        }

        //管理员地区获取及判断
        $admin = AdminUser::$user->toArray()[0];
        $admin = ["address" => $admin['address_id']];

        $company = Company::find($data['company_id']);
        if(empty($company)){
            return $this->response(
                [],
                ErrorCode::msg(ErrorCode::COMPANY_NOT_FOUND),
                ErrorCode::COMPANY_NOT_FOUND
            );
        }

        $company_arr = $company->toArray();

        if($admin["address"] != 440200 && $admin["address"] != $company_arr['county']){
            return $this->response(
                [],
                ErrorCode::msg(ErrorCode::FORBIDDEN),
                ErrorCode::FORBIDDEN
            );
        }

        if(isset($data['scale']) && !empty($data['scale'])){
            $company->scale = $data['scale'];
        }
        if(isset($data['operation_mode']) && !empty($data['operation_mode'])){
            $company->operation_mode = $data['operation_mode'];
        }
        if(isset($data['completed_at'])){
            if(empty($data['completed_at'])){
                $company->completed_at = null;
            }

        }
        if(isset($data['tecnology']) && !empty($data['tecnology'])){
            $company->tecnology = $data['tecnology'];
        }
        if(isset($data['water_quality']) && !empty($data['water_quality'])){
            $company->water_quality = $data['water_quality'];
        }
        if(isset($data['pipeline_length']) && !empty($data['pipeline_length'])){
            $company->pipeline_length = $data['pipeline_length'];
        }
        if(isset($data['address']) && !empty($data['address'])){
            $company->address = $data['address'];
        }
        if(isset($data['authority']) && !empty($data['authority'])){
            $company->authority = $data['authority'];
        }
        if(isset($data['leader']) && !empty($data['leader'])){
            $company->leader = $data['leader'];
        }
        if(isset($data['job']) && !empty($data['job'])){
            $company->job = $data['job'];
        }
        if(isset($data['contact']) && !empty($data['contact'])){
            $company->contact = $data['contact'];
        }

        if(isset($data['status']) && in_array($data['status'], [0,1])){
            $company->status = $data['status'];
        }
        $company->status = isset($data['status']) ? $data['status'] : 0;

        if($company->save()){
            return $this->response([]);
        }

        return $this->response(
            [],
            ErrorCode::msg(ErrorCode::SYSTEM_ERROR),
            ErrorCode::SYSTEM_ERROR
        );

    }
}