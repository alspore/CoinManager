<?php

/**
 * CoinManager module for the BitMinter pool.
 *
 * PHP version 5
 *
 * requires the cURL php library and simple_html_dom.php
 *
 * @author Aaron Landis <alspore@gmail.com>
 * @version 1.0
 * @package CoinManager.php
 * @subpackage cm_bitminter.php
 */

require_once('simple_html_dom.php');

class cm_bitminter extends CoinModule{
	private $json = null;

	function addWorker($name, $pass){
		if($this->debug){
			$this->time = microtime();
			$this->time = explode(' ', $this->time);
			$this->time = $this->time[1] + $this->time[0];
			$this->start = $this->time;
		}

		//action='...'
		$bitminter_workerpage = 'https://bitminter.com/members/workers';
		$bitminter_add_workerpage = 'https://bitminter.com/ajax_request/';

		//attempts to open worker page for scraping
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $bitminter_workerpage);
		curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_path);
		curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookie_path);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		$workerpage = curl_exec($curl);

		//scrapes 'token'
		$workerpage_html = str_get_html($workerpage);
		$post_fields = '';
		$workerpage_inputs = 0;

		foreach($workerpage_html->find('input') as $element){
			$workerpage_inputs++;
			if($workerpage_inputs == 1){
				$post_fields .= $element->name.'='.$name.'&';
			}elseif($workerpage_inputs == 2){
				$post_fields .= $element->name.'='.$pass.'&';
			}else{
				$post_fields .= $element->name.'='.$element->value.'&';
			}
		}

		foreach($workerpage_html->find('form') as $element){
			$bitminter_add_workerpage .= $element->id.'-00/';
		}

		curl_setopt($curl, CURLOPT_URL, $bitminter_add_workerpage);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
		curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_path);
		curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookie_path);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		$workertable = curl_exec($curl);
		curl_close($curl);

		if($this->debug){
			$log = array(
				'duplicate' => false,
				'response' => 0,
				'too_long' => false,
				'char_limit' => 20,
			);

			$this->time = microtime();
			$this->time = explode(' ', $this->time);
			$this->time = $this->time[1] + $this->time[0];
			$finish = $this->time;
			$log['response'] = round(($finish - $this->start), 4);

			echo $workertable;
			if(strstr($workertable, 'You cannot have two workers with the same name')){
				$log['duplicate'] = true;
			}elseif(strstr($workertable, 'Worker name cannot be longer than 20 characters')){
				$log['too_long'] = true;
			}
			return $log;
		}
	}

	function generateCookie(){

		//user info for myopenid
		$openid_identifier = 'http://'.$this->username.'.pip.verisignlabs.com';

		//action='...'
		$bitminter_login = 'https://bitminter.com/openid/login';
		$verisignlabs_server_login = 'http://pip.verisignlabs.com/server';
		$verisignlabs_login = 'https://pip.verisignlabs.com/login_user.do';

		if(file_exists($this->cookie_path)){
			unlink($this->cookie_path);
		}

		//post data for first_request
		$bitminter_loginpage_post_fields = 'openid_identifier='.$openid_identifier.'&openid_username='.$this->username;

		//attempts to post login form to bitminter
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $bitminter_login);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $bitminter_loginpage_post_fields);
		curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_path);
		curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookie_path);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
		$bitminter_loginpage = curl_exec($curl);
	
		//gets openid info from bitminter
		$bitminter_loginpage_html = str_get_html($bitminter_loginpage); //sets dom object from string
		$step2_post_fields = '';
		$first = true;
		foreach($bitminter_loginpage_html->find('input') as $element) { //sets openid post data scraped from bitminter
			if($first) { $step2_post_fields .= $element->name.'='.$element->value; $first = false; }
			else { $step2_post_fields .= '&'.$element->name.'='.$element->value; }
		}

		//attempts to get redirected from verisignlabs openid server
		curl_setopt($curl, CURLOPT_URL, $verisignlabs_server_login);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $step2_post_fields);
		curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_path);
		curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookie_path);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		$step2 = curl_exec($curl);

		//gets form data for verisignlabs login
		$step2_html = str_get_html($step2);
		$step3_post_fields = '';
		$first = true;

		//formats post data for login form
		foreach($step2_html->find('input') as $element){
			if($element->name == 'username'){
				$step3_post_fields .= $element->name.'='.$this->username;
			}elseif($element->name == 'password'){
				$step3_post_fields .= '&'.$element->name.'='.$this->password;
			}
		}

		//attempts to post login form
		curl_setopt($curl, CURLOPT_URL, $verisignlabs_login);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $step3_post_fields);
		curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_path);
		curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookie_path);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		$step3 = curl_exec($curl);
		curl_close($curl);

		if($this->debug){
			return $step3;
		}
	}

	function updateJson(){
		$raw = file_get_contents('http://bitminter.com/api/users/'.$this->username.'?key='.$this->api_key);
		$this->json = json_decode($raw, true);
	}

	function getActiveWorkers(){
		if(is_null($this->json)){ $this->updateJson(); }
		return $this->json['active_workers'];
	}

	function getHashRate($w){
		if(is_null($this->json)){ $this->updateJson(); }
		if(is_null($w)){ return $this->json['hash_rate']; }
		else{
			foreach ($this->json['workers'] as $worker){
				if($worker['name'] == $w){
					return $worker['hash_rate'];
				}
			}
			return null;
		}
	}

	function getAcceptedShares($w, $option){
		if(is_null($this->json)){ $this->updateJson(); }
		if(is_null($w)){ return $this->json['shift']['accepted']; }
		else{
			foreach ($this->json['workers'] as $worker){
				if($worker['name'] == $w){
					if($option == 'total'){ return $worker['work']['BTC']['total_accepted']; }
					else{ return $worker['work']['BTC']['round_accepted']; }
				}
			}
			return null;
		}
	}

	function getRejectedShares($w, $option){
		if(is_null($this->json)){ $this->updateJson(); }
		if(is_null($w)){ return $this->json['shift']['rejected']; }
		else{
			foreach ($this->json['workers'] as $worker){
				if($worker['name'] == $w){
					if($option == 'total'){ return $worker['work']['BTC']['total_rejected']; }
					else{ return $worker['work']['BTC']['round_rejected']; }
				}
			}
			return null;
		}
	}
}
?>