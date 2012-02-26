<?php
// define('MORIARTY_ARC_DIR', '../../arc/');
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'moriarty.inc.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'rollback.class.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'httprequest.class.php';

require_once MORIARTY_DIR. "changeset.class.php";


class RollbackTest extends PHPUnit_Framework_TestCase
{
	var $rollback;
	var $changes = array(
		
		'_:cs1' => array(
			    RDF_TYPE => array(array('value' => CS_CHANGESET, 'type' => 'uri')),
				CS_SUBJECTOFCHANGE => array(
					array('value' => 'http://example.org/#me', 'type' => 'uri'),
					),
				CS_REMOVAL => array( 
					array('value' => '_:s1', 'type' => 'bnode'),
					array('value' => '_:s2', 'type' => 'bnode'),
				 ),
			),
		'_:cs2' => array(
			RDF_TYPE => array(array('value' => CS_CHANGESET, 'type' => 'uri')),
			CS_SUBJECTOFCHANGE => array(
				array('value' => 'http://example.org/#me', 'type' => 'uri'),
				),
			
				CS_ADDITION => array( 
					array('value' => '_:s3', 'type' => 'bnode'),
					array('value' => '_:s4', 'type' => 'bnode'),				
			),
		),
	);
	
	var 	$expected = array(
			
			'_:cs1' => array(
					RDF_TYPE => array(array('value' => CS_CHANGESET, 'type' => 'uri')),
					CS_SUBJECTOFCHANGE => array(
						array('value' => 'http://example.org/#me', 'type' => 'uri'),
						),
					CS_CREATORNAME => array(array('value' => 'Moriarty Rollback Class', 'type' => 'literal')),  
					CS_CHANGEREASON => array(array('value' => 'Rollbacked Change, generated by Moriarty Rollback Class', 'type' => 'literal')),
					
					CS_ADDITION => array( 
						array('value' => '_:s1', 'type' => 'bnode'),
						array('value' => '_:s2', 'type' => 'bnode'),
					 ),
					CS_REMOVAL => array( 
						array('value' => '_:s3', 'type' => 'bnode'),
						array('value' => '_:s4', 'type' => 'bnode'),				
						),
				),
		);
	
	
  function setUp() {
	
	$sparqlservice = new FakeSparqlService();
	$this->rollback = new Rollback($sparqlservice);
	
	}
	
	function test_to_changeset(){
		
		
		require_once MORIARTY_ARC_DIR.'/ARC2.php';
		$ser = ARC2::getRDFXMLSerializer();
		$actual = $this->rollback->to_changeset('_:cs1')->body;
		$expected = $ser->getSerializedIndex($this->expected);
		$this->assertEquals($expected, $actual);		
	}


	function test_revert_changes(){
		
		$changes = $this->changes;
		
		$expected = $this->expected;
		
		$actual = $this->rollback->revert_changes($changes);
		
		$this->assertEquals($expected, $actual);
	}
	
}

class FakeSparqlService {
	
	function graph($query){
		require_once MORIARTY_ARC_DIR.'/ARC2.php';
		//$parser = ARC2::getRDFParser();
		
		$ser = ARC2::getRDFXMLSerializer();
		$body = false;
		$response = new HTTPResponse();
	//	if(stristr($query, 'DESCRIBE <#to_changeset>')){
			$r = new RollbackTest();
			$changes  = $r->changes;
			$body  = $ser->getSerializedIndex($changes);
	//	} else if(stristr($query, 'DESCRIBE ?cs ?statement')){
			// $r = new RollbackTest();
			// $changes  = $r->changes;
			// $body  = $ser->getSerializedIndex($changes);			
	//	}
		$response->status_code = '200';
		$response->body = $body;

		return $response;
	}
	
}

?>