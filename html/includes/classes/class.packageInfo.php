<?php

namespace nuke\fileLogger {
    
    /**
     * Package info
     * 
     * About this package.
     */
    class PackageInfo {
        
        protected static $packageInfo = array(
            'version' => 8.3,
            
            'authors' => array(
                'gehaxelt' => array(
                    'github' => 'https://github.com/php-nuke/php-nuke',
                    'email' => 'nuke.coders.exchange@gmail.com',
                    'site' => 'https://www.nuke.coders.exchange'
                ),
                
                'pedzed' => array(
                    'github' => 'https://github.com/php-nuke/php-nuke'
                )
            )
        );
        
        public static function getInfo(){
            return self::$packageInfo;
        }
        
    }
    
}
