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
}