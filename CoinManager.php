<?php
/*Config*/

//path to modules directory
$module_path = 'cm-modules/';

//list of module files to be loaded
$modules = array(
	'cm_bitminter',
);

/*Module Class*/

class CoinModule{
	public $username;
	public $password;
	public $cookie_path = 'cookie.txt';
	public $api_key;

	function setUsername($u){
		$this->username = $u;
	}

	function getUsername(){
		return $this->username;
	}

	function getPassword(){
		return $this->password;
	}

	function getCookiePath(){
		return $this->cookie_path;
	}

	function setPassword($p){
		$this->password = $p;
	}

	function setUserPass($u, $p){
		$this->username = $u;
		$this->password = $p;
	}

	function setCookiePath($cp){
		$this->cookie_path = $cp;
	}

	function setApiKey($key){
		$this->api_key = $key;
	}

	function getApiKey($key){
		return $this->api_key;
	}
}

/*Module Import*/

for($i = 1; $i <= count($modules); $i++){
	if(file_exists($module_path.$modules[$i-1]).'.php'){
		require_once($module_path.$modules[$i-1].'.php');
	}else{
		throw new Exception("Error Including Modules", 1);				
	}
}
?>