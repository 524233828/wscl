<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-05-01
 * Time: 22:34
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    protected $table = "wscl_companies";

    public function getCountyAverageScore()
    {

        return DB::table($this->table)
            ->select(DB::raw("county, AVG(score) as score"))
            ->groupBy("county")
            ->get()->toArray();

    }

    public function getCountyAverageScoreByMonth($month)
    {
        return DB::table($this->table)
            ->leftJoin("wscl_jsjd", $this->table.".id", "=", "wscl_jsjd.company_id")
            ->select(DB::raw("county, AVG(wscl_jsjd.score) as score"))
            ->where("month", "=", $month)
            ->groupBy("county")
            ->get()->toArray();
    }

    public function counties(){
        return $this->belongsTo(County::class, "county");
    }

    public static function getCompany()
    {
        $county = self::all(["id","name"])->toArray();
        $county_index_arr = [];
        foreach ($county as $value){
            $county_index_arr[$value['id']] = $value['name'];
        }
        return $county_index_arr;
    }
}