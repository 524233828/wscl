<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-07-24
 * Time: 14:58
 */

namespace App\Services\County\Score;


use Illuminate\Support\Facades\DB;

class NewestCompanyScore
{

    public static function get($month)
    {
        //首先取出所有污水厂最新的月份数据
        $sql = <<<SQL
SELECT 
  `company_id`, 
  max(`month`) as `month`
FROM 
  wscl_jsjd
WHERE 
  `month` <= {$month}
GROUP BY
  `company_id`
SQL;
        $result = DB::select($sql);

        //生成一个获取所有最新分数的sql
        $sqls = [];
        foreach ($result as $jsjd)
        {
            $sqls[] = "SELECT 
`company_id`, `month`, `score` 
FROM `wscl_jsjd` 
WHERE company_id='{$jsjd->company_id}' and `month`='{$jsjd->month}'";
        }

        //使用UNION ALL 链接所有结果集
        $sql = implode(" UNION ALL ", $sqls);

        $rel_sql = "SELECT county, AVG(jsjd.score) as score 
FROM wscl_companies wc
LEFT JOIN ({$sql}) as jsjd
ON wc.id=jsjd.company_id
GROUP BY county";

        $score = DB::select($rel_sql);

        $score_arr = [];
        foreach ($score as $value){
            $score_arr[$value->county] = floor($value->score);
        }

        return $score_arr;

    }
}