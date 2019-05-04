<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-05-02
 * Time: 21:15
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class BuildInfo extends Model
{

    protected $table = "wscl_jsjd";

    protected $fillable = ["company_id"];

    public function company()
    {
        return $this->belongsTo(Company::class,"company_id");
    }
}