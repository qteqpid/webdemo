<?php

/**
 * 
 * Copyright (c) 2009 by vastech.
 * Solr search factory
 * @author Peter
 * @version 1.0
 * @date 下午10:32:10 2011-8-9
 */
class SolrFactory {

	private static $instance = null;

    public static function getSolrInstance($solrpath) {
        if(self::$instance == null) {
        	require_once('Solr.php');
			self::$instance=new Solr($solrpath);
		}
        return self::$instance;
    }

    private function SolrFactory(){}

    private function __clone(){} 
}

?>
