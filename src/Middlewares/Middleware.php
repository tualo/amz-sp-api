<?php

namespace Tualo\Office\AmanzonSellerPartner\Middlewares;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\IMiddleware;

class Middleware implements IMiddleware{
    public static function register(){
        
        App::use('amz-sp-api',function(){
            try{
                if (isset($_GET['amazon_callback_uri'])){
                    $_SESSION['amazon_callback_uri'] = $_GET['amazon_callback_uri'];
                }
                if (
                    isset($_SESSION['tualoapplication']) && 
                    isset($_SESSION['tualoapplication']['loggedIn']) && 
                    ($_SESSION['tualoapplication']['loggedIn']===true) &&
                    isset($_SESSION['amazon_callback_uri'])
                ){
                    $url = $_SESSION['amazon_callback_uri'];
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