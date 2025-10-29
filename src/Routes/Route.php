<?php

namespace Tualo\Office\AmanzonSellerPartner\Routes;

use Tualo\Office\AmanzonSellerPartner\JsonQueryHelper;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;

class Route extends \Tualo\Office\Basic\RouteWrapper
{
    public static function register()
    {
        BasicRoute::add('/amz-sp-api/oauth', function ($matches) {
            App::result('msg', '');
            App::result('success', true);
            $db = App::get('session')->getDB();

            foreach ($_REQUEST as $key => $value) {
                $db->direct(
                    'insert into amazon_seller_partner_api (`keyname`,`value`) values ({keyname},{value}) on duplicate key update `value`=values(`value`)',
                    [
                        'keyname' => $key,
                        'value' => $value
                    ]
                );
            }
            $url = 'https://api.amazon.com/auth/o2/token';
            $data = JsonQueryHelper::query($url, 'grant_type=authorization_code&code=' . $_REQUEST['spapi_oauth_code'] . '&client_id=' . AMZ_CLIENT_ID . '&client_secret=' . AMZ_CLIENT_SECRET);

            //            $data = JsonQueryHelper::query($url,'grant_type=client_credentials&scope=sellingpartnerapi::migration&client_id='.AMZ_CLIENT_ID.'&client_secret='.AMZ_CLIENT_SECRET);
            //            print_r($data);
            //            exit();

            if (isset($data['access_token'])) {
                $db->direct(
                    'insert into amazon_seller_partner_api (`keyname`,`value`) values ({keyname},{value}) on duplicate key update `value`=values(`value`)',
                    [
                        'keyname' => 'access_token',
                        'value' => $data['access_token']
                    ]
                );
                $db->direct(
                    'insert into amazon_seller_partner_api (`keyname`,`value`) values ({keyname},{value}) on duplicate key update `value`=values(`value`)',
                    [
                        'keyname' => 'refresh_token',
                        'value' => $data['refresh_token']
                    ]
                );
                $db->direct(
                    'insert into amazon_seller_partner_api (`keyname`,`value`) values ({keyname},{value}) on duplicate key update `value`=values(`value`)',
                    [
                        'keyname' => 'token_type',
                        'value' => $data['token_type']
                    ]
                );
            } else {
                App::result('success', false);
                App::result('msg', 'Bitte versuchen Sie es erneut');
            }
            //file_put_contents(dirname(App::get('tempPath')).'/info.json',json_encode($_REQUEST));
            App::contenttype('application/json');
        }, ['get', 'post'], false);
    }
}
