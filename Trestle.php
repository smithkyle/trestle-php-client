<?php
/**
 * Trestle for PHP
 *
 * Copyright (c) 2011, Talldude Networks, LLC. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in the
 *   documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Trestle Class
 *
 * @link https://github.com/talldude/trestle-php-client
 * @version 0.0.1
 */
class Trestle
{
    // Credentials
    private static $__api_key     = null;   // Trestle API Key
    private static $__api_secret  = null;   // Trestle API Secret
    private static $__return_type = 'json'; // Default return type

    // Endpoints
    public static $user_service_url   = 'https://www.trestleapp.com/v1/user';
    public static $object_service_url = 'https://www.trestleapp.com/v1/object';
    public static $geo_service_url    = 'https://www.trestleapp.com/v1/geo';

    /**
     * Constructor
     *
     * @param string $accessKey Access key
     * @param string $secretKey Secret key
     * @return void
     */
    public function __construct($api_key = null, $api_secret = null)
    {
        if (!function_exists('curl_init')) {
            trigger_error('error: PHP cURL support is required for Trestle',E_USER_ERROR);
        }
        self::$__api_key    = $api_key;
        self::$__api_secret = $api_secret;
    }

    //-------------------------------
    // USER Service
    //-------------------------------

    /**
     * UserCreate - create a new user in the User Service
     *
     * @param array Account Creation parameters
     * @return mixed array on success, error message on failure
     */
    public function UserCreate(&$_args)
    {
        if (!isset($_args['username']{0}) && !isset($_args['email']{0})) {
            return 'error: username and/or email required';
        }
        if (!isset($_args['password']{0})) {
            return 'error: password required';
        }
        $tmp = $this->_request(self::$user_service_url,'POST',$_args);
        return $tmp;
    }

    /**
     * UserUpdate - update an existing user account in the User Service
     *
     * @param string User ID
     * @param array Account Creation parameters
     * @return mixed array on success, error message on failure
     */
    public function UserUpdate($user_id,&$_args)
    {
        return $this->_request(self::$user_service_url ."/{$user_id}",'PUT',$_args);
    }

    /**
     * UserDelete - delete an existing user from the User Service
     *
     * @param string User ID
     * @return mixed array on success, error message on failure
     */
    public function UserDelete($user_id)
    {
        return $this->_request(self::$user_service_url ."/{$user_id}",'DELETE');
    }

    /**
     * UserInfo - get info about specific user account
     *
     * @param string User ID
     * @return mixed array on success, error message on failure
     */
    public function UserInfo($user_id)
    {
        return $this->_request(self::$user_service_url ."/{$user_id}",'GET');
    }

    /**
     * UserSearch - Search user accounts
     *
     * @param array Match parameters
     * @return mixed array on success, error message on failure
     */
    public function UserSearch(&$_args)
    {
        return $this->_request(self::$user_service_url,'GET',$_args);
    }

    /**
     * UserLogin - login a user account
     *
     * @param string User ID
     * @return mixed array on success, error message on failure
     */
    public function UserLogin(&$_args)
    {
        if (!isset($_args['username']{0}) && !isset($_args['email']{0})) {
            return 'error: username and/or email required';
        }
        if (!isset($_args['password']{0})) {
            return 'error: password required';
        }
        return $this->_request(self::$user_service_url .'/login','GET');
    }

    //-------------------------------
    // Object Service
    //-------------------------------

    /**
     * ObjectCreate - create a new object in the Object Service
     *
     * @param string Collection
     * @param array Object Creation parameters
     * @return mixed array on success, error message on failure
     */
    public function ObjectCreate($collection,&$_args)
    {
        $tmp = $this->_request(self::$object_service_url ."/{$collection}",'POST',$_args);
        return $tmp;
    }

    /**
     * ObjectUpdate - update an existing object account in the Object Service
     *
     * @param string Collection
     * @param string Object ID
     * @param array Object Creation parameters
     * @return mixed array on success, error message on failure
     */
    public function ObjectUpdate($collection,$object_id,&$_args)
    {
        return $this->_request(self::$object_service_url ."/{$collection}/{$object_id}",'PUT',$_args);
    }

    /**
     * ObjectDelete - delete an existing object from the Object Service
     *
     * @param string Collection
     * @param string Object ID
     * @return mixed array on success, error message on failure
     */
    public function ObjectDelete($collection,$object_id)
    {
        return $this->_request(self::$object_service_url ."/{$collection}/{$object_id}",'DELETE');
    }

    /**
     * ObjectInfo - get info about specific object
     *
     * @param string Collection
     * @param string Object ID
     * @return mixed array on success, error message on failure
     */
    public function ObjectInfo($collection,$object_id)
    {
        return $this->_request(self::$object_service_url ."/{$collection}/{$object_id}",'GET');
    }

    /**
     * ObjectSearch - Search object accounts
     *
     * @param string Collection
     * @param array Match parameters
     * @return mixed array on success, error message on failure
     */
    public function ObjectSearch($collection,&$_args)
    {
        return $this->_request(self::$object_service_url ."/{$collection}",'GET',$_args);
    }

    //-------------------------------
    // Geo Service
    //-------------------------------

    /**
     * GeoInfo - get info about specific ip/hostname
     *
     * @param string IP Address or Hostname
     * @return mixed array on success, error message on failure
     */
    public function GeoInfo($ip)
    {
        return $this->_request(self::$geo_service_url ."/{$ip}",'GET');
    }


    //-------------------------------
    // Helper functions
    //-------------------------------

    /**
     * SetReturnType
     *
     * @param string return type of "json", "array", "object"
     * @return null
     */
    public function SetReturnType($type)
    {
        self::$__return_type = $type;
    }

    /**
     * _request - try/catch wrapper for _trestle_request
     *
     * @param string $message Error Message
     * @param string $file Error Filename
     * @param integer $line Error Line Number
     * @param integer $code Error Code
     * @return void
     */
    private function _request($url,$method,&$_args = false)
    {
        try {
            $_json = $this->_trestle_request($url,$method,$_args);
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
        return $_json;
    }

    /**
     * _trestle_request - use cURL to send a request to Trestle
     *
     * @param string $url Url to send request to
     * @param string $method Method of request (POST/GET/PUT/DELETE)
     * @param array $_args POST/PUT key => value params
     * @return array returns result array
     */
    private function _trestle_request($url,$method,&$_args = false) 
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_MAXREDIRS,3);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_VERBOSE,0);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch,CURLOPT_USERPWD,self::$__api_key .':'. self::$__api_secret);
        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch,CURLOPT_POST,true);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$_args);
                break;
            case 'GET':
                curl_setopt($ch,CURLOPT_POST,false);
                curl_setopt($ch,CURLOPT_URL,$url .'?'. http_build_query($_args));
                break;
            case 'PUT':
                curl_setopt($ch,CURLOPT_POSTFIELDS,$_args);
                curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
                break;
            case 'DELETE':
                curl_setopt($ch,CURLOPT_POSTFIELDS,$_args);
                curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'DELETE');
                break;
        }
        // Execute
        $json = curl_exec($ch);
        $code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code < 200 || $code > 299) {
            $_tmp = json_decode($json,true);
            throw new Exception($_tmp['error']);
        }
        switch (strtolower(self::$__return_type)) {
            case 'array':
                return json_decode($json,true);
                break;
            case 'object':
                return json_decode($json);
                break;
        }
        return $json;
    }
}
