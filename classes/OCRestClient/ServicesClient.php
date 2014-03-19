<?php
    require_once "OCRestClient.php";
    class ServicesClient extends OCRestClient
    {
        static $me;
        function __construct() {
            $this->serviceName = 'ServicesClient';
            if ($config = parent::getConfig('services')) {
                parent::__construct($config['service_url'],
                                    $config['service_user'],
                                    $config['service_password']);
            } else {
                throw new Exception (_("Die Konfiguration wurde nicht korrekt angegeben"));
            }
        }
        
        /**
         * getComponents() - retrieves episode system components from conntected Opencast-Matterhorn Core
         *
         *  @return array response of components
         */
        function getRESTComponents() {

            $service_url = "/services.json";
        
            if($result = $this->getJSON($service_url)){
                return $result->services->service;
            } else {
                return false;
            }
        }
    
    
    }

    
?>