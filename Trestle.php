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
    // Endpoints
    public $audio_service_url   = 'https://www.trestleapp.com/v1/audio';
    public $email_service_url   = 'https://www.trestleapp.com/v1/email';
    public $geo_service_url     = 'https://www.trestleapp.com/v1/geo';
    public $image_service_url   = 'https://www.trestleapp.com/v1/image';
    public $job_service_url     = 'https://www.trestleapp.com/v1/job';
    public $mailbox_service_url = 'https://www.trestleapp.com/v1/mailbox';
    public $object_service_url  = 'https://www.trestleapp.com/v1/object';
    public $s3_service_url      = 'https://www.trestleapp.com/v1/s3';
    public $stat_service_url    = 'https://www.trestleapp.com/v1/stat';
    public $user_service_url    = 'https://www.trestleapp.com/v1/user';

    // Error bucket
    public $errors              = false;

    // Credentials
    private $__api_key          = null;
    private $__api_secret       = null;

    // default return format from methods
    private $__return_type      = 'array';

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
        $this->__api_key    = $api_key;
        $this->__api_secret = $api_secret;
    }

    //-------------------------------
    // AUDIO service
    //-------------------------------
    /**
     * AudioMasterCreate - create a new Master Audio file
     *
     * @param array Master Audio file metadata
     * @return string|array|object on success, bool false on failure
     */
    public function AudioMasterCreate(&$_args)
    {
        if (!isset($_args['file']{0}) || !is_file($_args['file'])) {
            $this->SetError('error: audio file is required');
            return false;
        }
        if (!isset($_args['storage']{0})) {
            $this->SetError('error: storage parameter required');
            return false;
        }
        return $this->_request($this->audio_service_url,'POST',$_args);
    }

    /**
     * AudioMixCreate - create a new Mix from a Master Audio file
     *
     * @param string Master ID to create mix from
     * @param array Audio Mix Metadata
     * @return string|array|object on success, bool false on failure
     */
    public function AudioMixCreate($id,&$_args)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->audio_service_url ."/{$id}",'POST',$_args);
    }

    /**
     * AudioUpdate - Update meta information about an existing Master Audio file or Audio Mix
     *
     * @param string ID to update
     * @param array Metadata to save
     * @return string|array|object on success, bool false on failure
     */
    public function AudioUpdate($id,&$_args)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->audio_service_url ."/{$id}",'PUT',$_args);
    }

    /**
     * AudioInfo - Information about a Master Audio file or Audio Mix
     *
     * @param string ID to get info about
     * @return string|array|object on success, bool false on failure
     */
    public function AudioInfo($id)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->audio_service_url ."/{$id}",'GET');
    }

    /**
     * AudioList - list audio objects based on match parameters
     *
     * @param array List/Match parameters
     * @return string|array|object on success, bool false on failure
     */
    public function AudioList(&$_args)
    {
        return $this->_request($this->audio_service_url,'GET');
    }

    /**
     * AudioDelete - Delete a Master Audio file or Audio Mix
     *
     * @param string ID to delete
     * @return string|array|object on success, bool false on failure
     */
    public function AudioDelete($id)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->audio_service_url ."/{$id}",'DELETE');
    }


    //-------------------------------
    // EMAIL Service
    //-------------------------------
    /**
     * EmailSend - send an email through the mailer service
     *
     * @param string IP Address or Hostname
     * @return string|array|object on success, bool false on failure
     */
    public function EmailSend(&$_args)
    {
        if (!isset($_args['to']{0}) || !filter_var($_args['to'],FILTER_VALIDATE_EMAIL)) {
            $this->SetError('error: invalid To Email Address');
            return false;
        }
        if (!isset($_args['subject']{0})) {
            $this->SetError('error: Email subject required');
            return false;
        }
        if (!isset($_args['message']{0})) {
            $this->SetError('error: Email message required');
            return false;
        }
        if (isset($_args['from']{0}) || !filter_var($_args['from'],FILTER_VALIDATE_EMAIL)) {
            $this->SetError('error: invalid From Email Address');
            return false;
        }
        return $this->_request($this->email_service_url,'POST',$_args);
    }


    //-------------------------------
    // GEO Service
    //-------------------------------
    /**
     * GeoInfo - get info about specific ip/hostname
     *
     * @param string IP Address or Hostname
     * @return string|array|object on success, bool false on failure
     */
    public function GeoInfo($ip)
    {
        return $this->_request($this->geo_service_url ."/{$ip}",'GET');
    }


    //-------------------------------
    // IMAGE service
    //-------------------------------
    /**
     * ImageMasterCreate - create a new Master Image file
     *
     * @param array Master Image file metadata
     * @return string|array|object on success, bool false on failure
     */
    public function ImageMasterCreate(&$_args)
    {
        if (!isset($_args['file']{0}) || !is_file($_args['file'])) {
            $this->SetError('error: image file is required');
            return false;
        }
        if (!isset($_args['storage']{0})) {
            $this->SetError('error: storage parameter required');
            return false;
        }
        return $this->_request($this->image_service_url,'POST',$_args);
    }

    /**
     * ImageThumbCreate - create a new Thumbnail from a Master Image file
     *
     * @param string Master ID to create thumb from
     * @param array Image Thumbnail Metadata
     * @return string|array|object on success, bool false on failure
     */
    public function ImageThumbCreate($id,&$_args)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->image_service_url ."/{$id}",'POST',$_args);
    }

    /**
     * ImageUpdate - Update meta information about an existing Master Image file or Image Thumbnail
     *
     * @param string ID to update
     * @param array Metadata to save
     * @return string|array|object on success, bool false on failure
     */
    public function ImageUpdate($id,&$_args)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->image_service_url ."/{$id}",'PUT',$_args);
    }

    /**
     * ImageInfo - Information about a Master Image file or Image Thumbnail
     *
     * @param string ID to get info about
     * @return string|array|object on success, bool false on failure
     */
    public function ImageInfo($id)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->image_service_url ."/{$id}",'GET');
    }

    /**
     * ImageList - list image objects based on match parameters
     *
     * @param array List/Match parameters
     * @return string|array|object on success, bool false on failure
     */
    public function ImageList(&$_args)
    {
        return $this->_request($this->image_service_url,'GET');
    }

    /**
     * ImageDelete - Delete a Master Image file or Image Thumbnail
     *
     * @param string ID to delete
     * @return string|array|object on success, bool false on failure
     */
    public function ImageDelete($id)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->image_service_url ."/{$id}",'DELETE');
    }


    //-------------------------------
    // JOB Service
    //-------------------------------
    /**
     * JobInfo - get info about a specific media conversion job
     *
     * @param string Job ID
     * @return string|array|object on success, bool false on failure
     */
    public function JobInfo($id)
    {
        return $this->_request($this->job_service_url ."/{$id}",'GET');
    }

    /**
     * JobList - list jobs
     *
     * @param array List parameters
     * @return string|array|object on success, bool false on failure
     */
    public function JobList(&$_args)
    {
        return $this->_request($this->job_service_url,'GET',$_args);
    }


    //-------------------------------
    // MAILBOX Service
    //-------------------------------
    /**
     * MailboxCreate - create a new IMAP/POP3 mailbox
     *
     * @param array Mailbox parameters
     * @return string|array|object on success, bool false on failure
     */
    public function MailboxCreate(&$_args)
    {
        if (!isset($_args['mailbox']{0}) || !filter_var($_args['mailbox'],FILTER_VALIDATE_EMAIL)) {
            $this->SetError('error: invalid mailbox Email Address');
            return false;
        }
        if (!isset($_args['password']{2})) {
            $this->SetError('error: invalid mailbox password - must be at least 3 characters');
            return false;
        }
        return $this->_request($this->mailbox_service_url,'POST',$_args);
    }

    /**
     * MailboxInfo - get information about specific mailbox
     *
     * @param string Mailbox email address
     * @return string|array|object on success, bool false on failure
     */
    public function MailboxInfo($mailbox)
    {
        if (!isset($mailbox{0}) || !filter_var($mailbox,FILTER_VALIDATE_EMAIL)) {
            $this->SetError('error: invalid mailbox Email Address');
            return false;
        }
        return $this->_request($this->mailbox_service_url ."/{$mailbox}",'GET');
    }

    /**
     * MailboxList - list all mailboxes in App Domain
     *
     * @param array Match/List parameters
     * @return string|array|object on success, bool false on failure
     */
    public function MailboxList($_args = false)
    {
        return $this->_request($this->mailbox_service_url,'GET',$_args);
    }

    /**
     * MailboxUpdate - set password or forwarding address for a mailbox
     *
     * @param string Mailbox email address
     * @param array mailbox info to update
     * @return string|array|object on success, bool false on failure
     */
    public function MailboxUpdate($mailbox,&$_args)
    {
        if (!isset($mailbox{0}) || !filter_var($mailbox,FILTER_VALIDATE_EMAIL)) {
            $this->SetError('error: invalid mailbox Email Address');
            return false;
        }
        if (isset($_args['password']) && !isset($_args['password']{2})) {
            $this->SetError('error: invalid mailbox password - must be at least 3 characters');
            return false;
        }
        if (isset($_args['forward']) && !filter_var($_args['forward'],FILTER_VALIDATE_EMAIL)) {
            $this->SetError('error: invalid forward Email Address');
            return false;
        }
        return $this->_request($this->mailbox_service_url ."/{$mailbox}",'PUT',$_args);
    }

    /**
     * MailboxDelete - delete a mailbox from the App Domain
     *
     * @param string Mailbox email address
     * @return string|array|object on success, bool false on failure
     */
    public function MailboxDelete($mailbox)
    {
        return $this->_request($this->mailbox_service_url ."/{$mailbox}",'DELETE');
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
        $tmp = $this->_request($this->object_service_url ."/{$collection}",'POST',$_args);
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
    public function ObjectUpdate($collection,$id,&$_args)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->object_service_url ."/{$collection}/{$id}",'PUT',$_args);
    }

    /**
     * ObjectDelete - delete an existing object from the Object Service
     *
     * @param string Collection
     * @param string Object ID
     * @return mixed array on success, error message on failure
     */
    public function ObjectDelete($collection,$id)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->object_service_url ."/{$collection}/{$id}",'DELETE');
    }

    /**
     * ObjectInfo - get info about specific object
     *
     * @param string Collection
     * @param string Object ID
     * @return mixed array on success, error message on failure
     */
    public function ObjectInfo($collection,$id)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->object_service_url ."/{$collection}/{$id}",'GET');
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
        return $this->_request($this->object_service_url ."/{$collection}",'GET',$_args);
    }


    //-------------------------------
    // S3 service
    //-------------------------------
    /**
     * S3Create - upload a new file to S3 storage
     *
     * @param array S3 Thumbnail Metadata
     * @return string|array|object on success, bool false on failure
     */
    public function S3Create(&$_args)
    {
        if (!isset($_args['file']{0}) || !is_file($_args['file'])) {
            $this->SetError('error: file is required');
            return false;
        }
        return $this->_request($this->s3_service_url,'POST',$_args);
    }

    /**
     * S3Update - Update meta information about an existing S3 file
     *
     * @param string ID to update
     * @param array Metadata to save
     * @return string|array|object on success, bool false on failure
     */
    public function S3Update($id,&$_args)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->s3_service_url ."/{$id}",'PUT',$_args);
    }

    /**
     * S3Info - Information about an S3 file
     *
     * @param string ID to get info about
     * @return string|array|object on success, bool false on failure
     */
    public function S3Info($id)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->s3_service_url ."/{$id}",'GET');
    }

    /**
     * S3List - list s3 objects based on match parameters
     *
     * @param array List/Match parameters
     * @return string|array|object on success, bool false on failure
     */
    public function S3List(&$_args)
    {
        return $this->_request($this->s3_service_url,'GET');
    }

    /**
     * S3Delete - Delete a Master S3 file or S3 Thumbnail
     *
     * @param string ID to delete
     * @return string|array|object on success, bool false on failure
     */
    public function S3Delete($id)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->s3_service_url ."/{$id}",'DELETE');
    }


    //-------------------------------
    // STAT Service
    //-------------------------------
    /**
     * StatInfo - get services statistics for Trestle App
     *
     * @param string IP Address or Hostname
     * @return string|array|object on success, bool false on failure
     */
    public function StatInfo($service,&$_args = false)
    {
        return $this->_request($this->stat_service_url ."/{$service}",'GET',$_args);
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
            $this->SetError('error: username and/or email required');
            return false;
        }
        if (!isset($_args['password']{0})) {
            $this->SetError('error: password required');
            return false;
        }
        return $this->_request($this->user_service_url,'POST',$_args);
    }

    /**
     * UserUpdate - update an existing user account in the User Service
     *
     * @param string User ID
     * @param array Account Creation parameters
     * @return mixed array on success, error message on failure
     */
    public function UserUpdate($id,&$_args)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->user_service_url ."/{$id}",'PUT',$_args);
    }

    /**
     * UserDelete - delete an existing user from the User Service
     *
     * @param string User ID
     * @return mixed array on success, error message on failure
     */
    public function UserDelete($id)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->user_service_url ."/{$id}",'DELETE');
    }

    /**
     * UserInfo - get info about specific user account
     *
     * @param string User ID
     * @return mixed array on success, error message on failure
     */
    public function UserInfo($id)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->user_service_url ."/{$id}",'GET');
    }

    /**
     * UserSearch - Search user accounts
     *
     * @param array Match parameters
     * @return mixed array on success, error message on failure
     */
    public function UserSearch(&$_args)
    {
        return $this->_request($this->user_service_url,'GET',$_args);
    }

    /**
     * UserLogin - login a user account
     *
     * @param array User info
     * @return mixed array on success, error message on failure
     */
    public function UserLogin(&$_args)
    {
        if (!isset($_args['username']{0}) && !isset($_args['email']{0})) {
            $this->SetError('error: username and/or email required');
            return false;
        }
        if (!isset($_args['password']{0})) {
            $this->SetError('error: password required');
            return false;
        }
        return $this->_request($this->user_service_url .'/login','GET');
    }

    /**
     * UserForgot - Initiate forgot login process for user
     *
     * @param array User info
     * @return mixed array on success, error message on failure
     */
    public function UserForgot(&$_args)
    {
        if (!isset($_args['username']{0}) && !isset($_args['email']{0})) {
            $this->SetError('error: username and/or email required');
            return false;
        }
        return $this->_request($this->user_service_url .'/forgot','POST');
    }

    /**
     * UserResend - Resend the forgot login email to a user
     *
     * @param string User ID
     * @return mixed array on success, error message on failure
     */
    public function UserResend($id)
    {
        if (!$this->_check_id($id)) { return false; }
        return $this->_request($this->user_service_url ."/{$id}/resend",'POST');
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
        $this->__return_type = $type;
    }

    /**
     * SetError
     *
     * @param string error message
     * @return null
     */
    public function SetError($msg)
    {
        if (!$this->errors) {
            $this->errors = array();
        }
        $this->errors[] = $msg;
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
    private function _check_id($id)
    {
        if (strlen($id) === 15) {
            return true;
        }
        $this->SetError('error: invalid id - must be a valid, existing id');
        return false;
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
        curl_setopt($ch,CURLOPT_USERPWD,$this->__api_key .':'. $this->__api_secret);
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
        switch (strtolower($this->__return_type)) {
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
