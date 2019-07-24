<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-07-24
 * Time: 14:56
 */

namespace App\Services\County\Score;


use App\Models\Company;

class AverageScore
{

    public static function get($month)
    {
        $company = new Company();

        $score = $company->getCountyAverageScoreByMonth($month);

        $score_arr = [];
        foreach ($score as $value){
            $score_arr[$value->county] = floor($value->score);
        }

        return $score_arr;
    }
}