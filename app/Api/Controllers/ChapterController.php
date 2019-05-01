<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/10/27
 * Time: 20:49
 */

namespace App\Api\Controllers;


use App\Api\Logic\ChapterLogic;
use Illuminate\Http\Request;

class ChapterController extends BaseController
{
    public function addChapter(Request $request)
    {
        $data = $request->query->all();

        $this->validate($data, [
            "title" => "required|string",
            "content" => "required|string",
        ]);

        return $this->response(ChapterLogic::getInstance()->addChapter($data['title'], $data['content']));

    }
}