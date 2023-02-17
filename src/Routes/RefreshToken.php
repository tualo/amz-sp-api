<?php
namespace Tualo\Office\AmanzonSellerPartner\Routes;
use Tualo\Office\AmanzonSellerPartner\JsonQueryHelper;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use SellingPartnerApi\Authentication;

class RefreshToken implements IRoute{
    /*
    private const EMPTY_CONFIG = [
        'lwaClientId' => '',
        'lwaClientSecret' => '',
        'lwaRefreshToken' => '',
        'awsAccessKeyId' => '',
        'awsSecretAccessKey' => '',
        'endpoint' => Endpoint::EU_SANDBOX,
    ];
    */

    public static function register(){
        BasicRoute::add('/amz-sp-api/refresh-token',function($matches){
            App::result('msg', App::get('configuration') );
            App::result('success', true );
            App::contenttype('application/json');
        },['get','post'],false);
    }
}