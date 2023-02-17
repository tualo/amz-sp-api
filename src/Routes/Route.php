<?php
namespace Tualo\Office\AmanzonSellerPartner\Routes;
use Tualo\Office\AmanzonSellerPartner\JsonQueryHelper;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;

class Route implements IRoute{
    public static function register(){
        BasicRoute::add('/amz-sp-api/oauth',function($matches){
            App::result('msg', 'ok' );
            App::result('success', true );
            $db = App::get('session')->getDB();

            foreach($_REQUEST as $key => $value ){
                $db->direct('insert into amazon_seller_partner_api (`keyname`,`value`) values ({keyname},{value}) on duplicate key update `value`=values(`value`)',
                [
                    'keyname'=>$key,
                    'value'=>$value
                ]);
            }
            $url = 'https://api.amazon.com/auth/o2/token';
            App::result('x', JsonQueryHelper::query($url,'grant_type=authorization_code&code='.$_REQUEST['spapi_oauth_code'].'&client_id='.AMZ_CLIENT_ID.'&client_secret='.AMZ_CLIENT_SECRET));
            //file_put_contents(dirname(App::get('tempPath')).'/info.json',json_encode($_REQUEST));
            App::contenttype('application/json');
        },['get','post'],false);


    }
}