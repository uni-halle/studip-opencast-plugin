<?php
    require_once "OCRestClient.php";
    class SearchClient extends OCRestClient
    {
        static $me;
        public $serviceName = 'Search';
        function __construct() {

            if ($config = parent::getConfig('search')) {
                parent::__construct($config['service_url'],
                                    $config['service_user'],
                                    $config['service_password']);
            } else {
                throw new Exception (_("Die Searchservice Konfiguration wurde nicht im g�ltigen Format angegeben."));
            }
        }

        /**
         *  getEpisodes() - retrieves episode metadata for a given series identifier from conntected Opencast-Matterhorn Core
         *
         *  @param string series_id Identifier for a Series
         *
         *  @return array response of episodes
         */
        function getEpisodes($series_id) {

            $cache = StudipCacheFactory::getCache();
            $cache_key = 'oc_episodesforseries/'.$series_id;
            $episodes = $cache->read($cache_key);
            
            if($episodes === false){
                $service_url = "/series.json?id=".$series_id."&episodes=true&series=true&limit=0&offset=0";
                if($search = $this->getJSON($service_url)){
                    $x = "search-results";
                    $episodes = $search->$x->result;
                    $cache->write($cache_key, serialize($episodes), 7200);
                    return $episodes;
                } else return array();
            } else {
                return unserialize($episodes);
            }
        }

        /**
         *  getSeries() - retrieves episode metadata for a given series identifier from conntected Opencast-Matterhorn Core
         *
         *  @param string series_id Identifier for a Series
         *
         *  @return array response of series
         */
        function getSeries($series_id) {

            $service_url = "/series.json?id=".$series_id."&episodes=true&series=true";
            if($search = $this->getJSON($service_url)){
                //$x = "search-results";
                //$episodes = $search->$x->result;
                return $search;
            } else return false;
        }

        /**
         *  getAllSeries() - retrieves episode metadata for a given series identifier from conntected Opencast-Matterhorn Core
         *
         *  @param void
         *
         *  @return array response of series
         */
        function getAllSeries() {

            $service_url = "/series.json";
            if($series = $this->getJSON($service_url)){
                //$x = "search-results";
                //$episodes = $search->$x->result;
                return $series;
            } else return false;
        }




        // other functions

        /**
         *  getEpisodeCount -
         *
         *  @param string series_id Identifier for a Series
         *
         *  @return int number of episodes
         */
        function getEpisodeCount($series_id) {
            if($series = $this->getSeries($series_id)) {
                $x = "search-results";
                $count = $series->$x->total;
                return intval($count);
            } else return false;

        }
        
        
        function getBaseURL() {
           $base = $this->matterhorn_base_url;
           $url = preg_replace('/\/search/', '', $base);
           return $url;
        } 


    }
?>