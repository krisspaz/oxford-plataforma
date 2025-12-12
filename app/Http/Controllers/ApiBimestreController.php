<?php namespace App\Http\Controllers;

class ApiBimestreController extends \crocodicstudio\crudbooster\controllers\ApiController
{

    public function __construct()
    {
        $this->table       = "bimestres";
        $this->permalink   = "bimestre";
        $this->method_type = "get";
    }
        

    public function hook_before(&$postdata)
    {
        //This method will be execute before run the main process

    }

    public function hook_query(&$query)
    {
        //This method is to customize the sql query

    }

    public function hook_after($postdata, &$result)
    {
        //This method will be execute after run the main process

    }

}
