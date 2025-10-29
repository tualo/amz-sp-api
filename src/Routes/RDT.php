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


class RDT extends \Tualo\Office\Basic\RouteWrapper
{

    public static function register()
    {
        BasicRoute::add('/amz-sp-api/rdt', function ($matches) {
            // See README for more information on the Configuration object's options
            $db = App::get('session')->getDB();
            $config = (App::get('configuration'))['amazon'] + $db->directMap('select `keyname`,`value` from amazon_seller_partner_api', [], 'keyname', 'value');

            $amazon_config = new Configuration([
                'lwaClientId' => $config['AMZ_CLIENT_ID'],
                'lwaClientSecret' => $config['AMZ_CLIENT_SECRET'],
                'lwaRefreshToken' => $config['refresh_token'],
                'awsAccessKeyId' => $config['awsAccessKeyId'],
                'awsSecretAccessKey' => $config['awsSecretAccessKey'],
                'endpoint' => Endpoint::EU
            ]);
            $restricted_resources = ['/orders/v0/orders'];

            $apiInstance = new TokensV20210301Api($amazon_config);
            $body = new CreateRestrictedDataTokenRequest([
                'restricted_resources' => $restricted_resources
            ]);
            // \SellingPartnerApi\Model\TokensV20210301\CreateRestrictedDataTokenRequest | The restricted data token request details.

            try {
                $result = $apiInstance->createRestrictedDataToken($body);
                foreach ($restricted_resources as $path) {
                    $db->direct(
                        'insert into amazon_seller_partner_rdt_token (`path`,`token`,`valid_until`) 
                    values ({path},{token},{valid_until}) on duplicate key update `token`=values(`token`),`valid_until`=values(`valid_until`)',
                        [
                            'path' => $path,
                            'token' => $result->getRestrictedDataToken(),
                            'valid_until' => date('Y-m-d H:i:s', time() + intval($result->getExpiresIn()))
                        ]
                    );
                }
                App::result('result', $result);
            } catch (\Exception $e) {
                echo 'Exception when calling TokensV20210301Api->createRestrictedDataToken: ', $e->getMessage(), PHP_EOL;
            }
            App::contenttype('application/json');
        }, ['get', 'post'], false);
    }
}
