<?php
/* 

Utilities API ( Only for Non-Commercial Use )
By Kevin Wang
From China

Skype: kvwang98
Twitter: KevinWang_China
Youtube: http://www.youtube.com/VanishedKevin
E-Mail: kevin@cnkvha.com

*/
class PMEssUtilsAPI{
	private $server;

	public function __construct(){
		$this->server = ServerAPI::request();
	}
	public function init(){
	}
	
    public function startWith($haystack, $needle){
        return $needle === "" || strpos($haystack, $needle) === 0;
    }
    
    public function endWith($haystack, $needle){
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }


}

?> 
