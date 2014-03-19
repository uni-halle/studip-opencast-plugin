<?php
    require_once "OCRestClient.php";
    class SchedulerClient extends OCRestClient
    {
        static $me;
        function __construct() {
            $this->serviceName = 'SchedulerClient';
            if ($config = parent::getConfig('recordings')) {
                parent::__construct($config['service_url'],
                                    $config['service_user'],
                                    $config['service_password']);
            } else {
                throw new Exception (_("Die Schedulerservice Konfiguration wurde nicht im g�ltigen Format angegeben."));
            }
        }


        /**
         * scheduleEventForSeminar - schedules an event
         * TODO: Fix agentparameter
         * @param string $course_id  - course identifier
         * @param string $resource_id  - resource identifier
         * @param string $termin_id  - termin identifier
         * @return bool success or not
         */
        function scheduleEventForSeminar($course_id, $resource_id, $termin_id) {

            $dublincore = utf8_encode(OCModel::createScheduleEventXML($course_id, $resource_id, $termin_id));


            $date = new SingleDate($termin_id);
            $start_time = date('D M d H:i:s e Y', $date->getStartTime());



            $issues = $date->getIssueIDs();
            if(is_array($issues)) {
                foreach($issues as $is) {
                    $issue = new Issue(array('issue_id' => $is));
                }
            }

            if(!$issue->title) {
                $title = sprintf(_('Aufzeichnung vom %s'), $date->getDatesExport());
            } else $title = $issue->title;

            $room = ResourceObject::Factory($resource_id);
            $cas = OCModel::checkResource($resource_id);
            $ca = $cas[0];
            $device = $ca['capture_agent'];

            $agentparameters = '#Capture Agent specific data
                                #'. $start_time .'
                                event.title=' . $title .'
                                event.location=' . $room->name . '
                                capture.device.id=' . $device;
                   

            $post = array('dublincore' => $dublincore, 'agentparameters' => $agentparameters);


            $rest_end_point = "/";
            $uri = $rest_end_point;
            // setting up a curl-handler
            curl_setopt($this->ochandler,CURLOPT_URL,$this->matterhorn_base_url.$uri);
            curl_setopt($this->ochandler, CURLOPT_POST, true);
            curl_setopt($this->ochandler, CURLOPT_POSTFIELDS, $post);
            curl_setopt($this->ochandler, CURLOPT_HEADER, true);
            //TODO �ber REST Klasse laufen lassen, getXML, getJSON...

            $response = curl_exec($this->ochandler);
            $httpCode = curl_getinfo($this->ochandler, CURLINFO_HTTP_CODE);
            $resArray = explode("\n", $response);

            if ($httpCode == 201){
                $pttrn = '#Location: http:/'.$this->matterhorn_base_url.'/(.+?).xml#Uis';
                foreach($resArray as $resp) {

                    // THIS could be changed. Keep an eye on futre oc releases...
                    if(preg_match($pttrn, $resp, $matches)) {
                        $eventid = $matches[1];
                    }
                }
   
                OCModel::scheduleRecording($course_id, $resource_id, $termin_id, $eventid);
   
                return true;
            } else {
                return false;
            }
        }


        /**
         * delelteEventForSeminar -  deletes a scheduled event
         *
         * @param string $course_id  - course identifier
         * @param string $resource_id  - resource identifier
         * @param string $date_id  - termin identifier
         * @return bool success or not
         */
        function deleteEventForSeminar($course_id, $resource_id, $date_id) {



            $event_data = OCModel::checkScheduled($course_id, $resource_id, $date_id);
            $event = $event_data[0];

            $rest_end_point = "/".$event['event_id'];
            $uri = $rest_end_point;

            // setting up a curl-handler
            curl_setopt($this->ochandler,CURLOPT_URL,$this->matterhorn_base_url.$uri);
            curl_setopt($this->ochandler,CURLOPT_CUSTOMREQUEST, "DELETE");
//TODO �ber REST Klasse laufen lassen, getXML, getJSON...

            $response = curl_exec($this->ochandler);
            $httpCode = curl_getinfo($this->ochandler, CURLINFO_HTTP_CODE);


            if ($httpCode == 200){
                $event_id = $event['event_id'];
                OCModel::unscheduleRecording($event_id);

                return true;
            } else {
                return false;
            }
        }
    }
?>