<?php
namespace Tualo\Office\AmanzonSellerPartner\Routes;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;

class Route implements IRoute{
    public static function register(){
        BasicRoute::add('/amz-sp-api/oauth',function($matches){
            App::result('msg', App::get('tempPath').'/info.json' );
            App::result('success', true );
            file_put_contents(App::get('tempPath').'/info.json',json_encode($_REQUEST));
            App::contenttype('application/json');
        },['get','post'],false);


    }
}