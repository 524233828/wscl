<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/12/16
 * Time: 22:47
 */

namespace App\Services\CallableFunc;


use App\Models\CallableFunction;
use App\Services\CallableFunc\Constant\FuncType;
use App\Services\Service;

class CallFunctionService extends Service
{
    public function call($func_id, $parameters, $obj = null)
    {
        $function = CallableFunction::find($func_id);
        $func_name = $function->function_name;
        $func_type = $function->type;
        $class_name = $function->class_name;

        switch ($func_type)
        {
            case FuncType::CLOSURE_FUNC:
                $closure = config("function.{$func_name}");
                return $this->callClosureFunction($closure, $parameters);
                break;
            case FuncType::SIMPLE_FUNC:
                return $this->callSimpleFunction($func_name, $parameters);
                break;
            case FuncType::OBJ_FUNC:
                if(empty($obj))
                {
                    return $this->callObjFunction(new $class_name(), $func_name, $parameters);
                }else{
                    return $this->callObjFunction($obj, $func_name, $parameters);
                }
                break;
            case FuncType::CLASS_STATIC_FUNC:
                return $this->callStaticFunction($class_name, $func_name, $parameters);
        }

    }

    /**
     * 对象方法调用
     * @param $obj
     * @param $func_name
     * @param $parameters
     * @return mixed
     */
    protected function callObjFunction($obj, $func_name, $parameters)
    {
        return call_user_func_array([$obj, $func_name], $parameters);
    }

    /**
     * 匿名函数调用
     * @param \Closure $function
     * @param $parameters
     * @return mixed
     */
    protected function callClosureFunction(\Closure $function, $parameters)
    {
        return call_user_func_array($function, $parameters);
    }

    /**
     * 类的静态方法调用
     * @param $class_name
     * @param $func_name
     * @param $parameters
     * @return mixed
     */
    protected function callStaticFunction($class_name, $func_name, $parameters)
    {
        return call_user_func_array([$class_name, $func_name], $parameters);
    }

    /**
     * 普通的函数调用
     * @param $func_name
     * @param $parameters
     * @return mixed
     */
    protected function callSimpleFunction($func_name, $parameters)
    {
        return call_user_func_array($func_name, $parameters);
    }
}