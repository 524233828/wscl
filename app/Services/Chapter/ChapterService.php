<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/10/9
 * Time: 11:31
 */

namespace App\Services\Chapter;


use App\Models\ChapterModel;
use App\Services\Service;

class ChapterService extends Service
{
    public function addChapter($title, $content)
    {
        $data = [
            "title" => $title,
            "content" => $content,
        ];

        $model = new ChapterModel();

        $model->fill($data);

        $model->save();

        return "success";
    }

    public function fetchChapter($page = 1, $size = 20)
    {
    }
}