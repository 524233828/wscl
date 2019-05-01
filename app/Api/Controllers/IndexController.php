<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/11/21
 * Time: 15:02
 */

namespace App\Api\Controllers;


use App\Models\Card;
use App\Models\UserCard;
use function GuzzleHttp\Psr7\parse_query;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mushan\BaiduTongji\BaiduTongji;

class IndexController
{

    public function index(Request $request)
    {
//        $url = parse_url("http://jl.1sk1.cn");
//        $path_arr = explode("/", $url['path']);
//        dd($path_arr);
        /**
         * @var BaiduTongji $baiduTongji
         */
        $baiduTongji = resolve('BaiduTongji');
        $today = date('Ymd');
        $yesterday = date('Ymd', strtotime('yesterday'));

//        $result = $baiduTongji->getSiteLists();
        $result = $baiduTongji->getData([
            'site_id' => '13186253',
            'method' => 'visit/toppage/a',
            'start_date' => $yesterday,
            'end_date' => $yesterday,
            'metrics' => 'pv_count,visitor_count',
//            'viewType' => "type"
//            'gran' => 'day',
        ]);

        dd($result['items'][1]);

        var_dump($result['items'][1]);
        var_dump($result['sum']);
        var_dump($result['pageSum']);

    }

    public function getCard(Request $request)
    {
//        var_dump();
    }
}