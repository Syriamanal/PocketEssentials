<?php
/*
__PocketMine Plugin__
name=PocketEssentials-Warps
version=4.1.5-Alpha
author=Kevin Wang
class=PMEssWarps
apiversion=11,12
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

class PMEssWarps implements Plugin{
	private $api;
	private $dirConfig = false;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
		$this->Init_Folders();
		$this->api->console->register("warp", "Warp to a place. ", array($this, "handleCommand"));
		$this->api->console->register("setwarp", "Set a warp. ", array($this, "handleCommand"));
		$this->api->console->register("delwarp", "Delete a warp. ", array($this, "handleCommand"));
	}
	
	private function Init_Folders(){
		$this->dirConfig = $this->api->plugin->configPath($this)."/Warps";
		$this->api->file->SafeCreateFolder($this->dirConfig);
	}
	
	public function __destruct(){
	}
	
	public function handleCommand($cmd, $arg, $issuer, $alias){
		switch(strtolower($cmd)){
			case "warp":
				if(!($issuer instanceof Player)){return("Please run this command in-game. ");}
				if(count($arg) != 1){
					return("Usage: \n/warp <Name>");
				}
				$cfgPath = $this->dirConfig . "/" . strtolower($arg[0]) . ".yml";
				if(!(file_exists($cfgPath))){
					return("Warp doesn't exist. ");
				}
				$cfg = new Config($cfgPath, CONFIG_YAML, array());
				$lv = $this->api->level->get($cfg->get("LevelName"));
				if(!($lv instanceof Level)){
					return("Target world doesn't exist. ");
				}
				$x = (int) $cfg->get("PositionX");
				$y = (int) $cfg->get("PositionY");
				$z = (int) $cfg->get("PositionZ");
				$issuer->sendChat("Warping to " . strtolower($arg[0]) . "...");
				$issuer->teleport(new Position($x, $y, $z, $lv));
				break;
			case "setwarp":
				if(!($issuer instanceof Player)){return("Please run this command in-game. ");}
				if(count($arg) != 1){
					return("Usage: \n/setwarp <Name>");
				}
				$cfgPath = $this->dirConfig . "/" . strtolower($arg[0]) . ".yml";
				if(file_exists($cfgPath)){
					return("Warp already exists. ");
				}
				$cfg = new Config($cfgPath, CONFIG_YAML, array());
				$cfg->set("LevelName", $issuer->level->getName());
				$cfg->set("PositionX", (int)$issuer->entity->x);
				$cfg->set("PositionY", (int)$issuer->entity->y);
				$cfg->set("PositionZ", (int)$issuer->entity->z);
				$cfg->save();
				unset($cfg);
				return("Warp set! ");
				break;
			case "delwarp":
				if(!($issuer instanceof Player)){return("Please run this command in-game. ");}
				if(count($arg) != 1){
					return("Usage: \n/delwarp <Name>");
				}
				$cfgPath = $this->dirConfig . "/" . strtolower($arg[0]) . ".yml";
				if(!(file_exists($cfgPath))){
					return("Warp doesn't exist. ");
				}
				@unlink($cfgPath);
				return("Warp deleted! ");
				break;
		}
	}
	
	public function getWarp($warpName){
		$cfgPath = $this->dirConfig . "/" . strtolower($warpName) . ".yml";
		if(!(file_exists($cfgPath))){
			return(false);
		}
		$cfg = new Config($cfgPath, CONFIG_YAML, array());
		$lv = $this->api->level->get($cfg->get("LevelName"));
		if(!($lv instanceof Level)){
			return(false);
		}
		$x = (int) $cfg->get("PositionX");
		$y = (int) $cfg->get("PositionY");
		$z = (int) $cfg->get("PositionZ");
		return(new Position($x, $y, $z, $lv));
	}
	
}
?>
