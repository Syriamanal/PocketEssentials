<?php
/*
__PocketMine Plugin__
name=PocketEssentials-WorldSpawn
version=4.1.1-Alpha
author=Kevin Wang
class=PMEssWorldSpawn
apiversion=11
*/

/* 

By Kevin Wang
From China

Project Website: http://www.MineConquer.com/
Official Website: http://www.cnkvha.com/
Skype: kvwang98
Twitter: KevinWang_China
Youtube: http://www.youtube.com/VanishedKevin
E-Mail: kevin@cnkvha.com

*/

class PMEssWorldSpawn implements Plugin{
	private $api;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
		$this->api->console->register("setwspawn", "Set the spawn position for your current world. ", array($this, "handleCommand"));
		$this->api->console->alias("swspawn", "setwspawn", array($this, "handleCommand"));
	}
	
	public function __destruct(){
	}
	
	public function handleCommand($cmd, $arg, $issuer, $alias){
		switch(strtolower($cmd)){
			case "setwspawn":
				if(!($issuer instanceof Player)){return("Please run this command in-game. ");}
				$issuer->level->setSpawn(new Vector3((int)$issuer->entity->x, (int)$issuer->entity->y, (int)$issuer->entity->z));
				return("Spawn set for world: " . $issuer->level->getName());
				break;
		}
	}
	
}
?>
