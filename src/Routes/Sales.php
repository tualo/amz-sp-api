<?php

namespace Tualo\Office\AmanzonSellerPartner\Routes;

use Tualo\Office\AmanzonSellerPartner\JsonQueryHelper;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Authentication;
use SellingPartnerApi\Endpoint;
use SellingPartnerApi\Api\SalesV1Api;



class Sales extends \Tualo\Office\Basic\RouteWrapper
{

    public static function register()
    {
        BasicRoute::add('/amz-sp-api/sales', function ($matches) {
            $db = App::get('session')->getDB();
            $config = (App::get('configuration'))['amazon'] + $db->directMap('select `keyname`,`value` from amazon_seller_partner_api', [], 'keyname', 'value');

            App::result('hint', $config);
            App::result('success', false);

            $amazon_config = new Configuration([
                'lwaClientId' => $config['AMZ_CLIENT_ID'],
                'lwaClientSecret' => $config['AMZ_CLIENT_SECRET'],
                'lwaRefreshToken' => $config['refresh_token'],
                'awsAccessKeyId' => $config['awsAccessKeyId'],
                'awsSecretAccessKey' => $config['awsSecretAccessKey'],
                'endpoint' => Endpoint::EU,
            ]);
            $amazon_config->setDebug(true);
            $amazon_config->setDebugFile('./amazon_debug.log');




            $sales = new SalesV1Api($amazon_config);
            $data = $sales->getOrderMetricsRequest(
                [
                    $config['selling_partner_id']
                ],
                '2023-01-01T00:00:00-07:00--2023-01-31T00:00:00-07:00',
                'Hour',
                $granularity_time_zone = null,
                $buyer_type = 'All',
                $fulfillment_network = null,
                $first_day_of_week = 'monday',
                $asin = null,
                $sku = null
            );
            /*
            if (count($data)==2){
                
                $db->direct('insert into amazon_seller_partner_api (`keyname`,`value`) values ({keyname},{value}) on duplicate key update `value`=values(`value`)',
                [
                    'keyname'=>'access_token',
                    'value'=>$data[0]
                ]);
                $db->direct('insert into amazon_seller_partner_api (`keyname`,`value`) values ({keyname},{value}) on duplicate key update `value`=values(`value`)',
                [
                    'keyname'=>'access_token_valid_until',
                    'value'=>$data[1]
                ]);

                App::result('success', true );
            }
            */
            App::result('sales', $data);

            App::contenttype('application/json');
        }, ['get', 'post'], false);
    }
}
