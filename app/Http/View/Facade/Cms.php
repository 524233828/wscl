<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-04-28
 * Time: 16:21
 */

namespace App\Http\View\Facade;


use Illuminate\Support\Facades\Facade;

/**
 * Class Cms
 * @package App\Http\View\Facade
 * @method static \App\Http\View\Cms create(Callable $callback)
 */
class Cms extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Http\View\Cms::class;
    }
}