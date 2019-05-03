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
        $score->setRawAttributes($data);

        if($score->save()){
            return $this->response([]);
        }else{
            return $this->response(
                [],
                ErrorCode::msg(ErrorCode::SYSTEM_ERROR),
                ErrorCode::SYSTEM_ERROR
            );
        }
    }
}