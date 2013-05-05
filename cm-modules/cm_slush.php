<?php

/**
 * CoinManager module for Slush's bitcoin pool.
 *
 * PHP version 5
 *
 * requires the cURL php library and simple_html_dom.php
 *
 * @author Aaron Landis <alspore@gmail.com>
 * @version 1.0
 * @package CoinManager.php
 * @subpackage cm_slush.php
 */

require_once('simple_html_dom.php');

class cm_template extends CoinModule{
	private $json = null;

	function addWorker($name, $pass){
		//adds new worker
	}

	function generateCookie(){
		//generates logged in cookie
	}

	function updateJson(){
		//if api is a json file, updates local json object
	}

	function getActiveWorkers(){
		//returns number of active workers
	}

	function getHashRate($w){
		//returns total or specific hashrate
	}

	function getAcceptedShares($w, $option){
		//returns total or specific accepted shares
	}

	function getRejectedShares($w, $option){
		//returns total or specific rejected shares
	}
}
?>