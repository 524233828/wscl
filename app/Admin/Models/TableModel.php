<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/8/1
 * Time: 15:25
 */


namespace App\Admin\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class TableModel extends Model
{

    protected $primaryKey = "TABLE_NAME";

    public $incrementing = false;

    protected $keyType = "string";

    /**
     * @param null $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $perPage = Request::get('per_page', 20);

        $page = Request::get('page', 1);

        $start = ($page-1)*$perPage;

        $database = config("database.connections.mysql.database");

        // 运行sql获取数据数组
        $sql = <<<SQL
SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA="{$database}" LIMIT {$start}, {$perPage}
SQL;

        $result = DB::select($sql);

        $movies = static::hydrate($result);

        $count_sql = <<<SQL
SELECT COUNT(*) as num FROM information_schema.TABLES WHERE TABLE_SCHEMA="{$database}"
SQL;

        /**
         * @var \PDO $pdo
         */
        $pdo = DB::connection()->getPdo();

        $count_result = $pdo->query($count_sql)->fetchAll(\PDO::FETCH_ASSOC);

        $total = $count_result[0]['num'];

        $paginator = new LengthAwarePaginator($movies, $total, $perPage);

        $paginator->setPath(url()->current());

        return $paginator;
    }

    public static function with($relations)
    {
        return new static;
    }

    // 获取单项数据展示在form中
    public function findOrFail($id)
    {
        $data = file_get_contents("http://api.douban.com/v2/movie/subject/$id");

        $data = json_decode($data, true);

        return static::newFromBuilder($data);
    }
}