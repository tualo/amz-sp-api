<?php

namespace Tualo\Office\AmanzonSellerPartner\Routes;

use Tualo\Office\AmanzonSellerPartner\JsonQueryHelper;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Endpoint;
use SellingPartnerApi\Api\TokensV20210301Api;
use SellingPartnerApi\Model\TokensV20210301\CreateRestrictedDataTokenRequest;


class RDT implements IRoute
{

    public static function register()
    {
        BasicRoute::add('/amz-sp-api/rdt', function ($matches) {
            // See README for more information on the Configuration object's options
            $db = App::get('session')->getDB();
            $config = (App::get('configuration'))['amazon'] + $db->directMap('select `keyname`,`value` from amazon_seller_partner_api',[],'keyname','value');

            $amazon_config = new Configuration([
                'lwaClientId' => $config['AMZ_CLIENT_ID'],
                'lwaClientSecret' => $config['AMZ_CLIENT_SECRET'],
                'lwaRefreshToken' => $config['refresh_token'],
                'awsAccessKeyId' => $config['awsAccessKeyId'],
                'awsSecretAccessKey' => $config['awsSecretAccessKey'],
                'endpoint' => Endpoint::EU
            ]);

            $apiInstance = new TokensV20210301Api($amazon_config);
            $body = new CreateRestrictedDataTokenRequest([
                'restricted_resources'=>[
                    '/orders/v0/orders'
                ]
            ]);
            // \SellingPartnerApi\Model\TokensV20210301\CreateRestrictedDataTokenRequest | The restricted data token request details.
            
            try {
                $result = $apiInstance->createRestrictedDataToken($body);
                App::result('result', $result );
            } catch (\Exception $e) {
                echo 'Exception when calling TokensV20210301Api->createRestrictedDataToken: ', $e->getMessage(), PHP_EOL;
            }
            App::contenttype('application/json');
        }, ['get', 'post'], false);
    }
}
