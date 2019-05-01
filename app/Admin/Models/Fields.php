<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/12/5
 * Time: 17:26
 */

namespace App\Admin\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Fields extends Model
{
    public function getAllFieldsFromTable($table_name)
    {
        $sql = <<<SQL
SHOW FULL COLUMNS FROM {$table_name}
SQL;

        $result = DB::select($sql);

        return $result;
    }
}