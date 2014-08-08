<?php

class InitOcplugin extends Migration {
    function up() {
        
                   
        DBManager::get()->query("CREATE TABLE IF NOT EXISTS `oc_config` (
              `service_url` varchar(255) NOT NULL,
              `service_user` varchar(255) NOT NULL,
              `service_password` varchar(255) NOT NULL,
              PRIMARY KEY (`service_url`)
              ) ENGINE=MyISAM;");

        DBManager::get()->query("CREATE TABLE IF NOT EXISTS `oc_seminar_series` (
                `seminar_id` VARCHAR( 32 ) NOT NULL ,
                `series_id` VARCHAR( 64 ) NOT NULL ,
                `visibility` ENUM(  'visible',  'invisible' )NOT NULL ,
                `schedule` TINYINT( 1 ) NOT NULL DEFAULT '0',
                PRIMARY KEY (  `seminar_id` ,  `series_id` )
                ) ENGINE = MYISAM;");
        
        DBManager::get()->query("CREATE TABLE IF NOT EXISTS `oc_resources` (
                `resource_id` VARCHAR( 32 ) NOT NULL ,
                `capture_agent` VARCHAR( 64 ) NOT NULL ,
                PRIMARY KEY (  `resource_id` ,  `capture_agent` )
                ) ENGINE = MYISAM;");

        DBManager::get()->query("INSERT INTO `resources_properties`
                (`property_id`, `name`, `description`, `type`, `options`, `system`)
                VALUES (MD5('".uniqid()."'), 'automatische Vorlesungsaufzeichnung', '', 'bool', 'vorhanden', 0)");

        
        DBManager::get()->query("CREATE TABLE IF NOT EXISTS `oc_seminar_episodes` (
                `seminar_id` VARCHAR( 32 ) NOT NULL ,
                `episode_id` VARCHAR( 64 ) NOT NULL ,
                `visible` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'true',
                PRIMARY KEY ( `seminar_id` , `episode_id` )
                ) ENGINE = MYISAM;");


        DBManager::get()->query("CREATE TABLE IF NOT EXISTS `oc_scheduled_recordings` (
                `seminar_id` VARCHAR( 32 ) NOT NULL ,
                `series_id` VARCHAR( 64 ) NOT NULL ,
                `date_id` VARCHAR( 32 ) NOT NULL ,
                `resource_id` VARCHAR( 32 ) NOT NULL ,
                `capture_agent` VARCHAR( 64 ) NOT NULL ,
                `event_id` VARCHAR( 64 ) NOT NULL,
                `status` ENUM( 'scheduled', 'recorded' ) NOT NULL ,
                PRIMARY KEY ( `seminar_id` , `series_id` , `date_id` , `resource_id` , `capture_agent` )
                ) ENGINE = MYISAM;");


                 DBManager::get()->query("CREATE TABLE IF NOT EXISTS `oc_endpoints` (
                   `service_url` varchar(255) NOT NULL,
                   `service_host` varchar(255) NOT NULL DEFAULT '',
                   `service_type` varchar(255) NOT NULL DEFAULT '',
                   PRIMARY KEY (`service_url`)
                 ) ENGINE=MyISAM;");




    }
    
    function down() {
        DBManager::get()->query("DROP TABLE IF EXISTS `oc_series`,`oc_config`,`oc_endpoints`, `oc_seminar_series`, `oc_resources`, `oc_seminar_episodes`, `oc_scheduled_recordings`;");
        DBManager::get()->query("DELETE FROM  resources_objects_properties 
        WHERE property_id IN(SELECT property_id FROM resources_properties WHERE name = 'automatische Vorlesungsaufzeichnung' );");
        DBManager::get()->query("DELETE FROM resources_properties WHERE name = 'automatische Vorlesungsaufzeichnung';");
    }
    
}
