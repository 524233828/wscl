<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-05-02
 * Time: 09:25
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = "wscl_options";


    public function scoreItem()
    {
        return $this->belongsTo(ScoreItem::class);
    }
}