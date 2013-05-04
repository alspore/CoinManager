<?php
class CoinManager{

	//path to modules directory
	private $module_path = 'cm-modules/';
	
	//list of module files to be loaded
	private $modules = array(
		'cm-bitminter.php',
	);

	function __construct(){
		for($i = 1; $i <= count($this->modules); $i++){
			if(file_exists($this->module_path.$this->modules[$i-1])){
				require_once($this->module_path.$this->modules[$i-1]);
			}else{
				throw new Exception("Error Including Modules", 1);				
			}
		}
	}
}
?>