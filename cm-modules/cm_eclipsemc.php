<?php

/**
 * CoinManager module for the EclipseMC pool.
 *
 * PHP version 5
 *
 * requires the cURL php library and simple_html_dom.php
 *
 * @author Aaron Landis <alspore@gmail.com>
 * @version 1.0
 * @package CoinManager.php
 * @subpackage cm_eclipsemc.php
 */

require_once('simple_html_dom.php');

class cm_eclipsemc extends CoinModule{
	private $json = null;

	function addWorker($name, $pass){
		if(file_exists($this->cookie_path)){
			unlink($this->cookie_path);
		}

		$url2 = 'https://eclipsemc.com/manage_workers.php';
		$url1 = 'https://eclipsemc.com/confirm_login.php';
		$agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'username='.$this->username.'&password='.$this->password);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_path);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_path);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		$data2 = curl_exec($ch);
		curl_close($ch);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url2);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'new_worker_name='.$name.'&new_worker_password='.$pass.'&new_worker=yes');
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_path);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_path);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		$data2 = curl_exec($ch);
		curl_close($ch);

		return $data2;
	}

	function generateCookie(){
		if(file_exists($this->cookie_path)){
			unlink($this->cookie_path);
		}

		$url = 'https://eclipsemc.com/confirm_login.php';
		$agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'username='.$this->username.'&password='.$this->password);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_path);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_path);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
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