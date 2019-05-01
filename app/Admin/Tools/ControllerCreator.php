<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2018/12/6
 * Time: 19:12
 */

namespace App\Admin\Tools;


use Illuminate\Filesystem\Filesystem;

class ControllerCreator
{

    protected $files;

    protected $replace_prev = "{%";

    protected $replace_next = "%}";

    protected $params;

    protected $fields;

    /**
     * ControllerCreator constructor.
     * @param Filesystem $files
     * @param $params
     * @param $fields
     */
    public function __construct(Filesystem $files, $params, $fields)
    {
        $this->files = $files;
        $this->params = $params;
        $this->fields = $fields;
    }

    /**
     * @return bool
     */
    public function create()
    {
        $d = DIRECTORY_SEPARATOR;
        $path = base_path("{$d}app{$d}Admin{$d}Controllers");

        $parameter = [
            "datetime" => date("Y-m-d H:i:s"),
            "namespace" => $this->getNamespace($this->params['model']),
            "model" => $this->getClass($this->params['model']),
            "controller" => $this->params['controller'],
            "description" => $this->params['description'],
            "title" => $this->params['title'],
            "route" => $this->params['route'],
            "columns" => $this->getColumns(),
            "filter" => $this->getFilter(),
            "form" => $this->getForm()
        ];

        $path .= $d . $this->params['controller'] . ".php";

        $this->files->put($path, $this->stringReplace(
            $this->loadStub("{$d}app{$d}Admin{$d}stubs{$d}controller.stub"),
            $parameter
        ));

        return true;
    }
    /**
     * Get the full namespace for a given class, without the class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * 获取命名中的类名
     * @param $name
     * @return mixed
     */
    protected function getClass($name)
    {
        return str_replace($this->getNamespace($name).'\\', '', $name);
    }

    /**
     * 加载模板
     * @param $file
     * @return string
     */
    protected function loadStub($file)
    {
        return $this->files->get(base_path($file));
    }

    /**
     * 判断文件是否存在
     * @param $file
     * @return bool
     */
    protected function isFile($file)
    {
        return $this->files->exists(base_path($file));
    }

    /**
     * 模板变量替换
     * @param String $string
     * @param array $replacement
     * @return mixed
     */
    protected function stringReplace(String $string, array $replacement)
    {
        $search = [];
        $replace = [];

        foreach ($replacement as $key => $value){
            $search[] = $this->replace_prev.$key.$this->replace_next;
            $replace[] = $value;
        }
        try{
            return str_replace($search, $replace, $string);
        }catch (\Exception $e){

        }

    }

    /**
     * 生成列表显示字段代码
     * @return string
     */
    protected function getColumns()
    {

        $d = DIRECTORY_SEPARATOR;
        $columns = "";

        foreach ($this->params['field'] as $field_key)
        {
            if(isset($this->fields[$field_key])){
                $field = $this->fields[$field_key];

                $path = "{$d}app{$d}Admin{$d}stubs{$d}columns{$d}" . $this->getFieldType($field->Type) . ".stub";

                $parameter = [
                    "field" => $field->Field,
                    "sortable" => $this->getSortable($field_key),
                    "enum" => $this->getEnum($field_key),
                    "label" => $this->getLabel($field_key)
                ];

                if($this->isFile($path)){
                    $columns .= $this->stringReplace(
                        $this->loadStub($path),
                        $parameter
                    );
                }
            }
        }

        return $columns;
    }

    /**
     * 生成可筛选字段代码
     * @return string
     */
    protected function getFilter()
    {
        $d = DIRECTORY_SEPARATOR;
        $filter = "";

        foreach ($this->params['filter'] as $field_key)
        {
            if(isset($this->fields[$field_key])){
                $field = $this->fields[$field_key];

                $path = "{$d}app{$d}Admin{$d}stubs{$d}filters{$d}" . $this->getFieldType($field->Type) . ".stub";

                $parameter = [
                    "field" => $field->Field,
                    "enum" => $this->getEnum($field_key),
                    "label" => $this->getLabel($field_key)
                ];

                if($this->isFile($path)){
                    $filter .= $this->stringReplace(
                        $this->loadStub($path),
                        $parameter
                    );
                }
            }
        }

        return $filter;
    }

    /**
     * 生成表单代码
     * @return string
     */
    protected function getForm()
    {
        $d = DIRECTORY_SEPARATOR;
        $form = "";

        foreach ($this->fields as $field_key => $field)
        {
            if(isset($this->fields[$field_key])){
                $field = $this->fields[$field_key];

                if($field->Key == "PRI"){
                    $path = "{$d}app{$d}Admin{$d}stubs{$d}forms{$d}" . "pk.stub";
                }else{
                    $path = "{$d}app{$d}Admin{$d}stubs{$d}forms{$d}" . $this->getFieldType($field->Type) . ".stub";
                }

                $parameter = [
                    "field" => $field->Field,
                    "enum" => $this->getEnum($field_key),
                    "label" => $this->getLabel($field_key)
                ];

                if($this->isFile($path)){
                    $form .= $this->stringReplace(
                        $this->loadStub($path),
                        $parameter
                    );
                }
            }
        }

        return $form;
    }

    /**
     * 获取字段类型
     * @param $type
     * @return string
     */
    protected function getFieldType($type)
    {
        $explode = explode('(', $type);
        var_dump(trim($explode[0]));
        return trim($explode[0]);
    }

    /**
     * 获取标签
     * @param $field_key
     * @return string
     */
    protected function getLabel($field_key)
    {
        return isset($this->fields[$field_key]) ?
            (
                empty($this->fields[$field_key]->Comment) ?
                    $this->fields[$field_key]->Field :
                    $this->fields[$field_key]->Comment
            ) :
            "undefined";
    }

    /**
     * 获取排序模板
     * @param $field_key
     * @return string
     */
    protected function getSortable($field_key)
    {
        return in_array($field_key, $this->params['sortable']) ? "->sortable()" : "";
    }

    /**
     * 获取枚举值
     * @param $field_key
     * @return mixed
     */
    protected function getEnum($field_key)
    {
        if(isset($this->fields[$field_key]) && isset($this->params[$this->fields[$field_key]->Field])){
            return $this->params[$this->fields[$field_key]->Field];
        }

        return "[]";
    }

}