<?php


    require_once "OCRestClient.php";
    require_once $this->trails_root.'/models/OCModel.php';

    class SeriesClient extends OCRestClient
    {
        function __construct() {
            if ($config = parent::getConfig('series')) {
                parent::__construct($config['service_url'],
                                    $config['service_user'],
                                    $config['service_password']);
            } else {
                throw new Exception (_("Die Seriesservice Konfiguration wurde nicht im g�ltigen Format angegeben."));
            }
        }

        /**
         *  getAllSeries() - retrieves all series from conntected Opencast-Matterhorn Core
         *
         *  @return array response all series
         */
        function getAllSeries() {
            $service_url = "/series/all.json";
            if($series = self::getJSON($service_url)){
                return $series->seriesList;
            } else return false;
        }

        /**
         *  getSeries() - retrieves seriesmetadata for a given series identifier from conntected Opencast-Matterhorn Core
         *
         *  @param string series_id Identifier for a Series
         *
         *	@return array response of a series
         */
        function getSeries($series_id) {
            $service_url = "/series/".$series_id.".json";
            if($series = self::getJSON($service_url)){
                return $series->series;
            } else return false;
        }

        /**
         *  getSeriesDublinCore() - retrieves DC Representation for a given series identifier from conntected Opencast-Matterhorn Core
         *
         *  @param string series_id Identifier for a Series
         *
         *  @return string DC representation of a series
         */
        function getSeriesDublinCore($series_id) {

            $service_url = "/series/".$series_id."/dublincore";
            if($seriesDC = self::getXML($service_url)){
                // dublincore representation is returned in XML
                //$seriesDC = simplexml_load_string($seriesDC);
                return $seriesDC;

            } else return false;
        }


        /**
         * createSeriesForSeminar - creates an new Series for a given course in OC Matterhorn
         * @param string $course_id  - course identifier
         * @return bool sucess or not
         */
        function createSeriesForSeminar($course_id) {


            $xml = utf8_encode(OCModel::creatSeriesXML($course_id));

            $post = array('series' => $xml);


            $rest_end_point = "/series/?_method=put&";
            $uri = $rest_end_point;
            // setting up a curl-handler
            curl_setopt($this->ochandler,CURLOPT_URL,$this->matterhorn_base_url.$uri);
            curl_setopt($this->ochandler, CURLOPT_POST, true);
            curl_setopt($this->ochandler, CURLOPT_POSTFIELDS, $post);


            $response = curl_exec($this->ochandler);
            $httpCode = curl_getinfo($this->ochandler, CURLINFO_HTTP_CODE);

            if ($httpCode == 201){

                $new_series = json_decode($response);
                $series_id = $new_series->series->id;
                OCModel::setSeriesforCourse($course_id, $series_id, 'visible', 1);

                return true;
            } else {
                return false;
            }
        }




        // static functions...
        static function storeAllSeries($series_id) {
            $stmt = DBManager::get()->prepare("SELECT * FROM `oc_series` WHERE series_id = ?");
            $stmt->execute(array($series_id));
            if(!$stmt->fetch()) {
                $stmt = DBManager::get()->prepare("REPLACE INTO
                    oc_series (series_id)
                    VALUES (?)");
                return $stmt->execute(array($series_id));
            }
            else return false;
        }
    }
?>