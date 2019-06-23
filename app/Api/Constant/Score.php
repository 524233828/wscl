<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-05-03
 * Time: 08:35
 */

namespace App\Api\Constant;


class Score
{

    public static $score_list = [
        //选址
        "xz" => [
            //未完成
            0 => 0,
            //已完成
            1 => 10,
        ],
        "zd" => [
            0 => 0,
            //已完成
            1 => 20,
        ],
        "styp" => [
            0 => 0,
            1 => 5,
            2 => 10,
        ],
        "kt" => [
            0 => 0,
            //已完成
            1 => 10,
        ],
        "gwsg" => [
            0 => 0,
            1 => 5,
            2 => 10,
        ],
        "tjsg" => [
            0 => 0,
            1 => 10,
            2 => 15,
        ],
        "jdaz" => [
            0 => 0,
            1 => 5,
            2 => 10,
        ],
        "syx" => [
            0 => 0,
            //已完成
            1 => 10,
        ],
        "zsyx" => [
            0 => 0,
            1 => 5,
        ],
        "jsjd" => [
            0 => -10,
            1 => -5,
            2 => -5,
            3 => -5,
            4 => 0,
            5=> -15,
            6=> -20,
            7=> -25,
            8=> -30,
            9=> -35,
            10=> -40,
            11=> -45,
            12=> -50,
        ],
    ];

    public static function computer($data)
    {
        $score = 0;
        foreach ($data as $key => $value){
            if(isset(self::$score_list[$key][$value])){
                $score += self::$score_list[$key][$value];
            }
        }

        return $score;
    }

}