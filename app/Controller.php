<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/10/27
 * Time: 21:50
 */

namespace App;


class Controller
{
    protected function validate($data, $rule)
    {
        $validator = validator($data, $rule);

        if($validator->fails())
        {
            echo $validator->errors();
        }
    }
}