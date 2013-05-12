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
		$raw = file_get_contents('https://eclipsemc.com/api.php?key='.$this->api_key.'&action=userstats');
		$this->json = json_decode($raw);
	}

	function getActiveWorkers($w){
		if(is_null($this->json)){ $this->updateJson(); }
		if(!is_null($w)){ 
			foreach($this->json['workers'] as $worker){
				$shortname = explode('_', $worker['worker_name']);
				if($shortname[1] == $w && $worker['hash_rate'] > 0){
					return true;
				}else{
					return false;
				}
			}
		}
		else{
			$total = 0;
			foreach ($this->json['workers'] as $worker){
				if($worker['hash_rate'] > 0){
					$total++;
				}
			}
			return $total;
		}
	}

	function getHashRate($w){
		if(is_null($this->json)){ $this->updateJson(); }
		if(!is_null($w)){ 
			foreach($this->json['workers'] as $worker){
				$shortname = explode('_', $worker['worker_name']);
				if($shortname[1] == $w){
					return $worker['hash_rate'];
				}
			}
			return null;
		}else{
			$total = 0;
			foreach ($this->json['workers'] as $worker){
				if($worker['hash_rate'] > 0){
					$total += $worker['hash_rate'];
				}
			}
			return $total;
		}
	}

	function getAcceptedShares($w, $option){
		if(is_null($this->json)){ $this->updateJson(); }
		if($option == 'total'){
			if(!is_null($w)){
				foreach($this->json['workers'] as $worker){
					$shortname = explode('_', $worker['worker_name']);
					if($shortname[1] == $w){
						return $worker['total_shares'];
					}
				}
				return null;
			}else{
				$total = 0;
				foreach($this->json['workers'] as $worker){
					$total += $worker['total_shares'];
				}
			}
		}else{
			if(!is_null($w)){
				foreach($this->json['workers'] as $worker){
					$shortname = explode('_', $worker['worker_name']);
					if($shortname[1] == $w){
						return $worker['round_shares'];
					}
				}
				return null;
			}else{
				$total = 0;
				foreach($this->json['workers'] as $worker){
					$total += $worker['round_shares'];
				}
			}
		}
	}
}
?>