<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-05-02
 * Time: 16:34
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    protected $table = "wscl_county";

    public static function getCounty()
    {
        $county = self::all(["id","name"])->toArray();
        $county_index_arr = [];
        foreach ($county as $value){
            $county_index_arr[$value['id']] = $value['name'];
        }
        return $county_index_arr;
    }
}