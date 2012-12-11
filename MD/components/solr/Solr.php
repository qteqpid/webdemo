<?php
require_once( dirname(__FILE__).DS.'Solr'.DS.'Service.php' );

/**
 * 
 * Copyright (c) 2009 by vastech.
 * Solr search 
 * @author Peter
 * @version 1.0
 * @date 下午10:32:10 2011-8-9
 */
class Solr
{
	private $cons	= Array(
		'solr/user'=>FALSE,
		'solr/shop'=>FALSE,
		'solr/product'=>FALSE,
		'solr/album'=>FALSE,
		'solr/faq'=>FALSE,
		'solr/tag'=>FALSE
	);
	private $solr;
	private $solrhost='news.instreet.cn';
	private $solrport='7008';
	private $solrpath;
	public $fatal_error	= FALSE;
	private $debug_mode	= FALSE;
	private $debug_info	= FALSE;


	public function __construct($solrpath)
	{
		$this->solrpath	= $solrpath;
	}

	public function connect()
	{
		$this->solr=new Apache_Solr_Service($this->solrhost,$this->solrport,$this->solrpath);
		if( ! $this->solr->ping() ) {
			echo 'Search service not responding.';
			exit;
		}else{
			$this->cons[$this->solrpath]=true;
		}
		return $this->cons[$this->solrpath];
	}

    public function addGoods($parts){
		if(FALSE == $this->cons[$this->solrpath]) {
			$this->connect($this->solrpath);
		}
		$part = new Apache_Solr_Document();
		foreach ( $parts as $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $datum ) {
					$part->setMultiValue( $key, $datum );
				}
			}
			else {
				$part->$key = $value;
			}
		}
		try {
			$this->solr->addDocument( $part );
			$this->solr->commit();
		}
		catch ( Exception $e ) {
			echo $e->getMessage();
		}
	}
    public function optimize(){
            if(FALSE == $this->cons[$this->solrpath]) {
			$this->connect($this->solrpath);
		}
		try {
			$this->solr->optimize();
		}
		catch ( Exception $e ) {
			echo $e->getMessage();
		}
        }
        
	public function add($parts){
		if(FALSE == $this->cons[$this->solrpath]) {
			$this->connect($this->solrpath);
		}
		$part = new Apache_Solr_Document();
		foreach ( $parts as $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $datum ) {
					$part->setMultiValue( $key, $datum );
				}
			}
			else {
				$part->$key = $value;
			}
		}
		try {
			$this->solr->addDocument( $part );
			$this->solr->commit(true,true);
			$this->solr->optimize();
		}
		catch ( Exception $e ) {
			echo $e->getMessage();
		}
	}

	public function adds($parts){
		if(FALSE == $this->cons[$this->solrpath]) {
			$this->connect($this->solrpath);
		}
		$documents = array();
		foreach ( $parts as $item => $fields ) {
			$part = new Apache_Solr_Document();
			foreach ( $fields as $key => $value ) {
				if ( is_array( $value ) ) {
					foreach ( $value as $datum ) {
						$part->setMultiValue( $key, $datum );
					}
				}
				else {
					$part->$key = $value;
				}
			}
			$documents[] = $part;
		}
		try {
			$this->solr->addDocuments( $documents );
			$this->solr->commit(true,true);
			$this->solr->optimize();
		}
		catch ( Exception $e ) {
			echo $e->getMessage();
		}
	}

	public function deleteById($id){
		if(FALSE == $this->cons[$this->solrpath]) {
			$this->connect($this->solrpath);
		}
		try {
			$this->solr->deleteById($id);
			$this->solr->commit();
			$this->solr->optimize();
		}
		catch ( Exception $e ) {
			echo $e->getMessage();
		}
	}
	

	public function deleteAll(){
		if(FALSE == $this->cons[$this->solrpath]) {
			$this->connect($this->solrpath);
		}
		try {
			$this->solr->deleteByQuery("*:*");
			$this->solr->commit();
			$this->solr->optimize();
		}
		catch ( Exception $e ) {
			echo $e->getMessage();
		}
	}
	
        
    public function autoCompleteTags($query)
	{
		if(FALSE == $this->cons[$this->solrpath]) {
			$this->connect($this->solrpath);
		}
		$tagnames=array();
                $q=strtolower($query).'*';//最大匹配
		$response = $this->solr->search( $q, 0, 20);
		if ( $response->getHttpStatus() == 200 ) {
			if ( $response->response->numFound > 0 ) {
				foreach ( $response->response->docs as $doc ) {
					$tagnames[]=$doc->tagname;
				}
			}
		}
		else {
			echo $response->getHttpStatusMessage();
		}
		return $tagnames;
	}
    
	/**
	 * 用于社区搜索
	 */
	public function queryPage($query,$pageNo,$num)
	{
		if ($query == '*:*' || $query=='*') return array(0,array());
		if(FALSE == $this->cons[$this->solrpath]) {
			$this->connect($this->solrpath);
		}
		$ids=array();
		$response = $this->solr->search( $query, $pageNo, $num);
		if ( $response->getHttpStatus() == 200 ) {
			if ( $response->response->numFound > 0 ) {
				foreach ( $response->response->docs as $doc ) {
					$ids[]=$doc;
				}
			}
		}
		else {
			echo $response->getHttpStatusMessage();
		}
		return array($response->response->numFound,$ids);
	}
	
	public function query($query, $num = 0)
	{
		if ($query == '*:*' || $query=='*') return array(0,array());
		if(FALSE == $this->cons[$this->solrpath]) {
			$this->connect($this->solrpath);
		}
		$ids=array();
		if ($num == 0) $num = 1000;
		$response = $this->solr->search( $query, 0, $num);
		if ( $response->getHttpStatus() == 200 ) {
			if ( $response->response->numFound > 0 ) {
				foreach ( $response->response->docs as $doc ) {
					$ids[]=$doc;
				}
			}
		}
		else {
			echo $response->getHttpStatusMessage();
		}
		return array($response->response->numFound,$ids);
	}


}

?>
