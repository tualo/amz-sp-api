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
            $db = App::get('session')->getDB();
            $config = (App::get('configuration'))['amazon'] + $db->direct('select `keyname`,`value` from amazon_seller_partner_api',[],'keyname');

            App::result('success', $config['amazon'] );
            App::contenttype('application/json');
        },['get','post'],false);
    }
}