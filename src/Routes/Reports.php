<?php
namespace Tualo\Office\AmanzonSellerPartner\Routes;
use Tualo\Office\AmanzonSellerPartner\JsonQueryHelper;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Endpoint;
use SellingPartnerApi\Api\SellersV1Api as SellersApi;
use SellingPartnerApi\Api\ReportsV20210630Api as Reports;

class ReportsRoute implements IRoute{
    
    public static function register(){
        BasicRoute::add('/amz-sp-api/reports',function($matches){
            $db = App::get('session')->getDB();
            $config = (App::get('configuration'))['amazon'] + $db->directMap('select `keyname`,`value` from amazon_seller_partner_api',[],'keyname','value');

            App::result('hint', $config );
            App::result('success', false );

            $amazon_config = new Configuration([
                'lwaClientId' => $config['AMZ_CLIENT_ID'],
                'lwaClientSecret' => $config['AMZ_CLIENT_SECRET'],
                'lwaRefreshToken' => $config['refresh_token'],
                'awsAccessKeyId' => $config['awsAccessKeyId'],
                'awsSecretAccessKey' => $config['awsSecretAccessKey'],
                'endpoint' => Endpoint::EU,
                "roleArn" => $config['roleArn']
            ]);

            /*
            SellingPartnerApi\Api\AuthorizationV1Api
            getAuthorizationCode($selling_partner_id=$config['selling_partner_id'], $developer_id, $mws_auth_token)
            */
            $api = new Reports($amazon_config);
            try {
                $max_results_per_page = 100;
                $financial_event_group_started_before = null;
                $financial_event_group_started_after = null;
                $next_token = null;
                // $result = $api->getMarketplaceParticipations();
                $result = $api->getReport($reportid=86423762542672);
                /*
                

                $apiInstance = new FinancesV0Api($config);
                try {
                    $result = $apiInstance->listFinancialEventGroups($max_results_per_page, $financial_event_group_started_before, $financial_event_group_started_after, $next_token);
                    
                    App::result('result',$result);
                } catch (\Exception $e) {
                    echo 'Exception when calling FinancesV0Api->listFinancialEventGroups: ', $e->getMessage(), PHP_EOL;
                }
                */

            } catch (\Exception $e) {
                echo 'Exception when calling xyz: ', $e->getMessage(), PHP_EOL;
            }


            App::contenttype('application/json');
        },['get','post'],false);
    }
}

