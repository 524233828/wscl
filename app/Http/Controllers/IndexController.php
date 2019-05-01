<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/11/21
 * Time: 10:22
 */

namespace App\Http\Controllers;


use App\Http\View\Banner;
use App\Http\View\Container;
use App\Http\View\Facade\Cms;
use App\Http\View\ModuleGroup;
use App\Models\Menu;

class IndexController
{
    public function index()
    {

        return Cms::create(function(\App\Http\View\Cms $cms){

            $title = "首页";
            $cms->title($title);

            $cms->setCss([
                "css/web/base.css",
                "css/web/tab.css",
                "css/web/module_1.css",
                "css/web/module_2.css",
                "css/web/module_3.css",
                "css/web/module_4.css",
                "css/web/module_6.css",
                "css/web/module_7.css",
                "css/web/module_8.css",
                "css/web/footer.css",
            ]);

            $cms->setJs([
                "/js/jquery.js",
            ]);

            $container = $cms->container(function(Container $container) use ($cms){
                $container->addChild($cms->header());
                $container->addChild($cms->menu(Menu::class));
                $container->addChild($cms->banner(Menu::class));


                $content = $cms->content();
                $container->addChild($content);

                $module_group = new ModuleGroup();
                $module_group->addChild();

                $content->addChild($module_group);

            });

//            var_dump($container->getChildren());


        })->render();

//         view("web.framework", ["title" => "首页", "css" => $css]);
    }
}