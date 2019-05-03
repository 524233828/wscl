<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-05-02
 * Time: 09:26
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ScoreItem extends Model
{
    protected $table = "wscl_score_items";

    public function options()
    {
        return $this->hasMany(Option::class, "item_id");
    }
}