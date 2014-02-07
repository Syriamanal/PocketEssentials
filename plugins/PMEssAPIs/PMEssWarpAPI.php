<?php
/* 

Warp API ( Only for Non-Commercial Use )
By Kevin Wang
From China

Skype: kvwang98
Twitter: KevinWang_China
Youtube: http://www.youtube.com/VanishedKevin
E-Mail: kevin@cnkvha.com

*/
class PMEssWarpAPI{
	private $server;
	private $moduleWarp;
	public function __construct(){
		$this->server = ServerAPI::request();
	}
	public function init(){
		$plugins = $this->server->api->plugin->getAll();
		foreach($plugins as $pl){
			if($pl[0] instanceof PMEssWarps){
				$this->moduleWarp = $pl[0];
				console("[WarpAPI] Successfully got Warps plugin's instance. ");
				return;
			}
		}
		console("[WarpAPI] Faild to get Warps plugin's instance. Any WarpAPI access maybe will crash your server. ");
	}
	
	public function getWarp($warpName){
		return($this->moduleWarp->getWarp($warpName));
	}

}

?> 
