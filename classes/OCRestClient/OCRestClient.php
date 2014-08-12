<?php
    /***
     * OCRestClient.php - The administarion of the opencast player
     * Copyright (c) 2011  Andr� Kla�en
     *
     * This program is free software; you can redistribute it and/or
     * modify it under the terms of the GNU General Public License as
     * published by the Free Software Foundation; either version 2 of
     * the License, or (at your option) any later version.
     */

    class OCRestClient
    {
        static $me;
        protected $matterhorn_base_url;
        protected $username;
        protected $password;
        public $serviceName = 'ParentRestClientClass';
        
        static function getInstance()
        {
            if(!property_exists(get_called_class(), 'me')) {
                throw new Exception('Every child of '.get_class().' needs to implement static property "$me"');
            }
            if (!is_object(static::$me)) {
                static::$me = new static();
            }
            return static::$me;
        }
        
        function __construct($matterhorn_base_url = null, $username = null, $password = null){
            $this->matterhorn_base_url = $matterhorn_base_url;
            
            $this->username = !is_null($username) ? $username : 'matterhorn_system_account';
            $this->password = !is_null($password) ? $password : 'CHANGE_ME';


            // setting up a curl-handler
            $this->ochandler = curl_init();
            curl_setopt($this->ochandler, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($this->ochandler, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
            curl_setopt($this->ochandler, CURLOPT_USERPWD, $this->username.':'.$this->password);
            curl_setopt($this->ochandler, CURLOPT_ENCODING, "UTF-8");
            curl_setopt($this->ochandler, CURLOPT_HTTPHEADER, array("X-Requested-Auth: Digest"));

            //ssl
            //curl_setopt($this->ochandler, CURLOPT_SSL_VERIFYPEER, false);
            //curl_setopt($this->ochandler, CURLOPT_SSL_VERIFYHOST, false);
            //curl_setopt($this->ochandler, CURLOPT_SSL_CIPHER_LIST, 'RC4-SHA');

            // debugging
            //curl_setopt($this->ochandler, CURLOPT_VERBOSE, true);
        }

        /**
          * function getConfig  - retries configutation for a given REST-Service-Client
          *
          * @param string $service_type - client label
          *
          * @return array configuration for corresponding client
          *
          */
        function getConfig($service_type) {
            
            if(isset($service_type)) {
                $stmt = DBManager::get()->prepare("SELECT * FROM `oc_endpoints` WHERE service_type = ?");
                $stmt->execute(array($service_type));
                $config = $stmt->fetch(PDO::FETCH_ASSOC);
                if($config) {
                $stmt = DBManager::get()->prepare("SELECT `service_user`, `service_password`  FROM `oc_config` WHERE 1");
                $stmt->execute();
                $config = $config + $stmt->fetch(PDO::FETCH_ASSOC);
                return $config;
                } else {
                    throw new Exception(sprintf(_("Es sinde keine Konfigurationsdaten f�r den Servicetyp **%s** vorhanden."), $service_type));
                }
                
            } else {
                throw new Exception(_("Es wurde kein Servicetyp angegeben."));
            }
        }

        /**
         *  function setConfig - sets config into DB for given REST-Service-Client
         *
         *	@param string $service_url
         *	@param string $service_user
         *  @param string $service_password
         */
        function setConfig($service_url, $service_user, $service_password) {
            if(isset($service_url, $service_user, $service_password)) {                    
                $stmt = DBManager::get()->prepare("REPLACE INTO `oc_config`  (service_url, service_user, service_password) VALUES (?,?,?)");
                return $stmt->execute(array($service_url, $service_user, $service_password));
            } else {
                throw new Exception(_('Die Konfigurationsparameter wurden nicht korrekt angegeben.'));
            }

        }
        
        function clearConfig($host = null) {
            $stmt = DBManager::get()->prepare("DELETE FROM `oc_config` WHERE 1;");
            $stmt->execute();
            $stmt = DBManager::get()->prepare("DELETE FROM `oc_endpoints` WHERE 1;");
            return $stmt->execute();
        }

        /**
         *  function getJSON - performs a REST-Call and retrieves response in JSON
         */
        function getJSON($service_url, $data = array(), $is_get = true, $with_res_code = false) {
            if(isset($service_url) && self::checkService($service_url)) {
                $options = array(CURLOPT_URL => $this->matterhorn_base_url.$service_url,
                           CURLOPT_FRESH_CONNECT => 1);
                if(!$is_get) {
                    $options[CURLOPT_POST] = 1;
                    if(!empty($data)) {
                        $options[CURLOPT_POSTFIELDS] = $data;
                    }
                } else {
                    $options[CURLOPT_HTTPGET] = 1;
                }
                         
                curl_setopt_array($this->ochandler, $options);
                $response = curl_exec($this->ochandler);
                $httpCode = curl_getinfo($this->ochandler, CURLINFO_HTTP_CODE);

                if($with_res_code) {
                    return array(json_decode($response), $httpCode);
                } else {
                    if ($httpCode == 404){
                        return false;
                    } else {
                        return json_decode($response);
                    }
                }
            } else {
                throw new Exception(_("Es wurde keine Service URL angegben"));
            }

        }
        
        /**
         * function getJSON - performs a REST-Call and retrieves response in JSON
         */
        function getXML($service_url, $data = array(), $is_get = true, $with_res_code = false) {
            if(isset($service_url) && self::checkService($service_url)) {
                $options = array(CURLOPT_URL => $this->matterhorn_base_url.$service_url,
                           CURLOPT_FRESH_CONNECT => 1);
                if(!$is_get) {
                    $options[CURLOPT_POST] = 1;
                    if(!empty($data)) {
                        $options[CURLOPT_POSTFIELDS] = $data;
                    }
                } else {
                    $options[CURLOPT_HTTPGET] = 1;
                }
                curl_setopt_array($this->ochandler, $options);
                $response = curl_exec($this->ochandler);
                $httpCode = curl_getinfo($this->ochandler, CURLINFO_HTTP_CODE);
                
                if($with_res_code) {
                    return array($response, $httpCode);
                } else {
                    if ($httpCode == 404){
                        return false;
                    } else {
                        return $response;
                    }
                }
            } else {
                throw new Exception(_("Es wurde keine Service URL angegben"));
            }
        }

        /**
         * function checkService - checks the status of desired REST-Endpoint
         *
         *  @param string $service_url
         *
         *  @return boolean $status
         */
        function checkService() {
            return true;
            if (@fsockopen($this->matterhorn_base_url)) {
                return true;
            }          
            throw new Exception(sprintf(_('Es besteht momentan keine Verbindung zum gew�hlten Service "%s". Versuchen Sie es bitte zu einem sp�teren Zeitpunkt noch einmal. Sollte dieses Problem weiterhin auftreten kontaktieren Sie bitte einen Administrator'), $this->serviceName));
        }
        
     

    }
?>
