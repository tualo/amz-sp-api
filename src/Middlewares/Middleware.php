<?php

namespace Tualo\Office\AmanzonSellerPartner\Middlewares;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\IMiddleware;

class Middleware implements IMiddleware{
    public static function register(){
        
        App::use('amz-sp-api',function(){
            try{
                if (isset($_GET['amazon_callback_uri'])){
                    $db = App::get('session')->getDB();
                    $_SESSION['amazon_callback_uri'] = $_GET['amazon_callback_uri'];
                    $_SESSION['amazon_state'] = $_GET['amazon_state'];
                    $_SESSION['amazon_version'] = $_GET['version'];
                    $_SESSION['selling_partner_id'] = $_GET['selling_partner_id'];
                    $_SESSION['tualo_state_for_amazon'] = $db->singleValue('select uuid() u',[],'u');
                }
                if (
                    isset($_SESSION['tualoapplication']) && 
                    isset($_SESSION['tualoapplication']['loggedIn']) && 
                    ($_SESSION['tualoapplication']['loggedIn']===true) &&
                    isset($_SESSION['amazon_callback_uri'])
                ){
                    $url = $_SESSION['amazon_callback_uri']
                    .'&amazon_state='.$_SESSION['amazon_state']
                    .'&state='.$_SESSION['tualo_state_for_amazon']
                    .'&version='.$_SESSION['amazon_version'];

                    $db->direct('create table if not exists amazon_seller_partner_api (keyname varchar(50) primary key, `value` varchar(4000) );');
                    $db->direct('insert into amazon_seller_partner_api (`keyname`,`value`) values ({keyname},{value}) on duplicate key update `value`=values(`value`)',
                    [
                        'keyname'=>'amazon_version',
                        'value'=>$_SESSION['amazon_version']
                    ]);
                    $db->direct('insert into amazon_seller_partner_api (`keyname`,`value`) values ({keyname},{value}) on duplicate key update `value`=values(`value`)',
                    [
                        'keyname'=>'amazon_state',
                        'value'=>$_SESSION['amazon_state']
                    ]);
                    $db->direct('insert into amazon_seller_partner_api (`keyname`,`value`) values ({keyname},{value}) on duplicate key update `value`=values(`value`)',
                    [
                        'keyname'=>'amazon_callback_uri',
                        'value'=>$_SESSION['amazon_callback_uri']
                    ]);
                    $db->direct('insert into amazon_seller_partner_api (`keyname`,`value`) values ({keyname},{value}) on duplicate key update `value`=values(`value`)',
                    [
                        'keyname'=>'selling_partner_id',
                        'value'=>$_SESSION['selling_partner_id']
                    ]);
                    $db->direct('insert into amazon_seller_partner_api (`keyname`,`value`) values ({keyname},{value}) on duplicate key update `value`=values(`value`)',
                    [
                        'keyname'=>'tualo_state_for_amazon',
                        'value'=>$_SESSION['tualo_state_for_amazon']
                    ]);

                    unset($_SESSION['amazon_state']);
                    unset($_SESSION['amazon_version']);
                    unset($_SESSION['selling_partner_id']);
                    unset($_SESSION['amazon_callback_uri']);
                    session_commit();
                    header('Location: '.$url);
                    die();
                }
                
            }catch(\Exception $e){
                App::set('maintanceMode','on');
                App::addError($e->getMessage());
            }
        },200);
    }
}