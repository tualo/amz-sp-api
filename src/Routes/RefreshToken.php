<?php
namespace Tualo\Office\AmanzonSellerPartner\Routes;
use Tualo\Office\AmanzonSellerPartner\JsonQueryHelper;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Authentication;
use SellingPartnerApi\Endpoint;

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
            $config = (App::get('configuration'))['amazon'] + $db->directMap('select `keyname`,`value` from amazon_seller_partner_api',[],'keyname','value');

            App::result('hint', $config );
            App::result('success', false );

            $amazon_config = [
                'lwaClientId' => $config['AMZ_CLIENT_ID'],
                'lwaClientSecret' => $config['AMZ_CLIENT_SECRET'],
                'lwaRefreshToken' => $config['refresh_token'],
                'awsAccessKeyId' => $config['awsAccessKeyId'],
                'awsSecretAccessKey' => $config['awsSecretAccessKey'],
                'endpoint' => Endpoint::EU,
            ];

            $auth = new Authentication($amazon_config); 
            if (count($auth)==2){
                
                $db->direct('insert into amazon_seller_partner_api (`keyname`,`value`) values ({keyname},{value}) on duplicate key update `value`=values(`value`)',
                [
                    'keyname'=>'access_token',
                    'value'=>$auth[0]
                ]);
                $db->direct('insert into amazon_seller_partner_api (`keyname`,`value`) values ({keyname},{value}) on duplicate key update `value`=values(`value`)',
                [
                    'keyname'=>'access_token_valid_until',
                    'value'=>$auth[1]
                ]);

                App::result('success', true );
            }
            App::result('auth',$auth->requestLWAToken());

            App::contenttype('application/json');
        },['get','post'],false);
    }
}