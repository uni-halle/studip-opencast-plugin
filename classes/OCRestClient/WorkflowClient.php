<?php
    require_once "OCRestClient.php";
    class WorkflowClient extends OCRestClient
    {
        static $me;
        function __construct() {
            if ($config = parent::getConfig('workflow')) {
                parent::__construct($config['service_url'],
                                    $config['service_user'],
                                    $config['service_password']);
            } else {
                throw new Exception (_("Die Konfiguration wurde nicht korrekt angegeben"));
            }
        }
        
        /**
         * getInstances() - returns all Workflow instances for a given SeriesID
         *
         *  @return array Workflow Instances
         */
        function getInstances($seriesID) {

            $service_url = sprintf( "/instances.json?state=&q=&seriesId=%s&seriesTitle=&creator=&contributor=&fromdate=&todate=&language="
                         . "&license=&title=&subject=&workflowdefinition=&mp=&op=&sort=&startPage=0&count=1000&compact=true", $seriesID);

            if($instances = $this->getJSON($service_url)){
                return $instances;
            } else {
                return false;
            }
        }
    
    
    }

    
?>



