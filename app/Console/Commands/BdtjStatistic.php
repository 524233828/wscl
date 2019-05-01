<?php

namespace App\Console\Commands;

use App\Models\BdtjPage;
use App\Models\FcChannel;
use App\Models\FcForecast;
use function GuzzleHttp\Psr7\parse_query;
use function GuzzleHttp\Psr7\uri_for;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;
use Mushan\BaiduTongji\BaiduTongji;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BdtjStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bdtj:statistic {--day=*} {--site_id=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '百度统计数据获取脚本';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    public function handle()
    {
//        ini_set("memory_limit", "128M");

        $day = isset($this->option("day")[0]) ?
            date('Ymd',strtotime($this->option("day")[0])) :
            date('Ymd', strtotime('yesterday'));
        $site_id = isset($this->option("site_id")[0]) ? $this->option("site_id")[0] : 13186253;

        $day = date('Ymd',strtotime($day));

        $_SERVER['HTTP_USER_AGENT'] = "";
        /**
         * @var BaiduTongji $baiduTongji
         */
        $baiduTongji = resolve('BaiduTongji');

//        $result = $baiduTongji->getSiteLists();
        $result = $baiduTongji->getData([
            'site_id' => $site_id,
            'method' => 'visit/toppage/a',
            'start_date' => $day,
            'end_date' => $day,
            'metrics' => 'pv_count,visitor_count',
            'searchWord' => "lath qtt"
        ]);

        dd($result);

        $page_list = $result['items'][0];

        //页面信息，页面只记录channel这一参数，其他不记录
        $local_pages = BdtjPage::all();

        $local_page_id = [];
        foreach ($local_pages as $local_page){
            $local_page_id[] = $local_page['page_id'];
        }

        //测算信息
        $forecasts = FcForecast::all();
        $forecast_name_id_list = [];
        foreach ($forecasts as $forecast){
            $forecast_name_id_list[$forecast['view_uri']] = $forecast['id'];
        }

        //当前当天数据
        $reports = \App\Models\BdtjStatistic::where("day", "=", $day)->get();

        $report_index_list = [];
        foreach ($reports as $report){
            $key = $report['day']."-" . $report['channel']. "-". $report['forecast_id'];
            $report_index_list[] = $key;
        }

        $time = date("Y-m-d H:i:s");
        $new_page_list = [];
        $statistic = [];
        foreach ($page_list as $key => $page){
            if(!in_array($page[0]['pageId'], $local_page_id)){
                $new_page_list[] = [
                    "page_id" =>$page[0]['pageId'],
                    "name" => $page[0]['name'],
                    "created_at" => $time,
                    "updated_at" => $time,
                    "site_id" => $site_id,
                    "status" => 1
                ];
            }
            $forecast_id = 0;
            $channel = "default";
            $url = parse_url($page[0]['name']);
            if(isset($url['path'])){
                $path_arr = explode("/", $url['path']);
                $forecast_uri = isset($path_arr[1]) ? $path_arr[1] : "";
                $forecast_id = isset($forecast_name_id_list[$forecast_uri]) ? $forecast_name_id_list[$forecast_uri] : 0;
            }

            if(isset($url['query'])){
                $query = parse_query($url['query']);

                $channel = isset($query['channel']) ? $query['channel'] : "default";
            }

            if($forecast_id != 0 && !in_array($day."-".$channel."-".$forecast_id, $report_index_list) && strlen($channel) <= 127){
                $statistic[] = [
                    "page_id" => $page[0]['pageId'],
                    "day" => $day,
                    "channel" => $channel,
                    "forecast_id" => $forecast_id,
                    "created_at" => $time,
                    "updated_at" => $time,
                    "pv" => $result['items'][1][$key][0],
                    "uv" => $result['items'][1][$key][1],
                ];
            }

        }


        DB::table("bdtj_pages")->insert($new_page_list);

        DB::table("bdtj_statistics")->insert($statistic);


    }
}
