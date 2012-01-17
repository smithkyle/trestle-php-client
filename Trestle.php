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
    private static $__api_key    = null; // Trestle API Key
    private static $__api_secret = null; // Trestle API Secret

    // Endpoints
    public static $user_service_url = 'https://www.trestleapp.com/v1/user';

    /**
     * Constructor
     *
     * @param string $accessKey Access key
     * @param string $secretKey Secret key
     * @return void
     */
    public function __construct($api_key = null, $api_secret = null)
    {
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
        $tmp = $this->_request(self::$user_service_url,'POST',$_args);
        return $tmp;
    }

    /**
     * UserUpdate - update an existing user account in the User Service
     *
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
     * @param array Account Creation parameters
     * @return mixed array on success, error message on failure
     */
    public function UserDelete($user_id)
    {
        return $this->_request(self::$user_service_url ."/{$user_id}",'DELETE');
    }

    //-------------------------------
    // Helper functions
    //-------------------------------

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
        return $json;
    }
}
