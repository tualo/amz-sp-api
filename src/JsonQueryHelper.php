<?php
namespace Tualo\Office\AmanzonSellerPartner;
use Tualo\Office\Basic\TualoApplication as App;

class JsonQueryHelper {
    public static $last_error_nr = 0;
    public static $last_error_message = '';
    public static $last_data = '';
    public static $last_rawdata = '';
    
    
    public static function query($url,  $post=NULL){
        self::$last_data = self::raw($url,  $post);
        if (self::$last_data===false) return false;

        $object = json_decode(self::$last_data,true);
        if (
            !is_null($object)
        ){
            return $object;
        }else{
            self::$last_error_nr = -100;
            self::$last_error_message = "Backend server false response";
            syslog(LOG_ALERT, "Backend server false response $url ".self::$last_data);
            return false;
        }
    }

    public static function raw($url,  $post=NULL){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_NOBODY, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if ( !is_null($post) ) curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $cookie_file = App::get('tempPath').'/api_cookie';
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        if (file_exists($cookie_file)) curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

        $data = curl_exec($ch);
        self::$last_rawdata=$data;
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        if (!in_array($httpCode,[200,403,404,400])){
            self::$last_error_nr = $httpCode;
            self::$last_error_message = "Backend server problems";
            syslog(LOG_ALERT, "httpCode $httpCode query $url ");
            return false;
        }
        return $data;
    }


    public static function querySingleItem($url){
        $object = self::query($url);
        if (
            isset($object["data"]) &&
            count($object["data"])==1
        ){
            return $object["data"][0];
        }else{
            self::$last_error_nr = -101;
            self::$last_error_message = "Backend server false response (querySingleItem) ";
            return false;
        }
    }


}