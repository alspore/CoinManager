<?php

/**
 * CoinManager module for the BTCGuild pool.
 *
 * PHP version 5
 *
 * requires the cURL php library and simple_html_dom.php
 *
 * @author Aaron Landis <alspore@gmail.com>
 * @version 1.0
 * @package CoinManager.php
 * @subpackage cm_btcguild.php
 */

require_once('simple_html_dom.php');

class cm_btcguild extends CoinModule{
	private $json = null;

	function addWorker($name, $pass){
		$url = 'http://www.btcguild.com/index.php?page=workers';
		$agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_path);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_path);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		$data1 = curl_exec($ch);
		curl_close($ch);

		$data1 = str_get_html($data1);
		$post1 = '';
		foreach($data1->find('input') as $element){
			if($element->name == 'secret_token'){
				$post1 .= 'secret_token='.$element->value.'&new_worker='.$name;
			}
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post1);
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

		$url = 'http://www.btcguild.com/index.php?page=login';
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
		$raw = file_get_contents('https://www.btcguild.com/api.php?api_key='.$this->api_key);
		$this->json = json_decode($raw);
	}

	function getActiveWorkers($w){
		if(is_null($this->json)){ $this->updateJson(); }
		if(!is_null($w)){ 
			foreach($this->json['workers'] as $worker){
				$shortname = explode('_', $worker['worker_name']);
				if($shortname == $w && $worker['hash_rate'] > 0){
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
				if($shortname == $w){
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

	function getAcceptedShares($w){
		if(is_null($this->json)){ $this->updateJson(); }
		if(!is_null($w)){
			foreach($this->json['workers'] as $worker){
				$shortname = explode('_', $worker['worker_name']);
				if($shortname == $w){
					return $worker['valid_shares'];
				}
			}
			return null;
		}else{
			$total = 0;
			foreach($this->json['workers'] as $worker){
				$total += $worker['valid_shares'];
			}
		}
	}

	function getRejectedShares($w, $option){
		if(is_null($this->json)){ $this->updateJson(); }
		if(!is_null($w)){
			foreach($this->json['workers'] as $worker){
				$shortname = explode('_', $worker['worker_name']);
				if($shortname == $w){
					return $worker['stale_shares'];
				}
			}
			return null;
		}else{
			$total = 0;
			foreach($this->json['workers'] as $worker){
				$total += $worker['stale_shares'];
			}
		}
	}
}
?>