<?php

/*
__PocketMine Plugin__
name=PMEssentials-Protect
version=4.0.0-Beta
author=Kevin Wang
class=PMEssProtect
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


class PMEssProtect implements Plugin{
private $api, $config, $path, $pos1, $pos2, $level;public function __construct(ServerAPI $api, $server =false){
$this->api =$api; $this->pos1 =array(); $this->pos2 =array(); $this->level =array();
}
public function init() {
	$this->createConfig(); 
	$this->api->console->register("unprotect", "Unprotects your private area.", array($this, "dsgf54ew5"));
	$this->api->console->register("selworld", "Select whole world to protect.", array($this, "dsgf54ew5")); 
	$this->api->console->register("protect", "Protects the area for you.", array($this, "dsgf54ew5")); 
	$this->api->addHandler("player.block.touch", array($this, "b7ds5g4"), 7); 
	$this->api->addHandler("player.block.place", array($this, "b7ds5g4"), 7); 
	$this->api->addHandler("player.block.break", array($this, "b7ds5g4"), 7); 
	$this->api->console->alias("ppos1", "protect pos1"); 
	$this->api->console->alias("ppos2", "protect pos2");
}
public function __destruct(){
}
public function dsgf54ew5($cmd, $params, $issuer, $alias){
	$output =""; 
	if ($issuer instanceof Player) {$user =$issuer->iusername; 
		switch($cmd){
			//Protect whole world
			case "selworld":
				if($this->api->ban->isOp($issuer->iusername) == false){return("This command can only use by OPs. ");}
				$this->level[$user][0] =$issuer->level->getName();
				$this->level[$user][1] =$issuer->level->getName();
				$this->pos1[$user] =array(0, 0, 0);
				$this->pos2[$user] =array(256, 128, 256);
				return("Whole world selected! \nType /protect set [Protection ID] to protect this world. ");
				break;
			case "protect": 
				$mode =array_shift($params); 
				switch ($mode) {
					case "set": 
						if(!isset($this->pos1[$user]) || !isset($this->pos2[$user])){
							$output .= "Make a selection first. \nUsage: /protect <pos1 | pos2>\nor /ppos1 and /ppos2"; 
							break;
						}elseif ($this->level[$user][0] !== $this->level[$user][1]) {
							$output .= "The selection points exist on another world!"; 
							break;
						}
						$pid = (string) array_shift($params); 
						if($pid == ""){
							return("Usage: \n/protect set [Protection ID]");
						}
						if(isset($this->config[$user][$this->level[$user]][$pid])){
							return("Protection ID already exists in this world! ");
						}
						$pos1 =$this->pos1[$user]; 
						$pos2 =$this->pos2[$user]; 
						$minX =min($pos1[0], $pos2[0]); 
						$maxX =max($pos1[0], $pos2[0]); 
						$minY =min($pos1[1], $pos2[1]); 
						$maxY =max($pos1[1], $pos2[1]); 
						$minZ =min($pos1[2], $pos2[2]); 
						$maxZ =max($pos1[2], $pos2[2]); 
						$max =array($maxX, $maxY, $maxZ); 
						$min =array($minX, $minY, $minZ); 
						if(!(isset($this->config[$user]))){
							$this->config[$user] = array();
						}
						if(!(isset($this->config[$user][$this->level[$user][0]]))){
							$this->config[$user][$this->level[$user][0]] = array();
						}
						$this->config[$user][$this->level[$user][0]][$pid] =array("min" => $min, "max" => $max, "share"=>array()); 
						$this->writeConfig($this->config); 
						$output .= "Protected this area ($minX, $minY, $minZ)-($maxX, $maxY, $maxZ)";
						if(isset($this->pos1[$user])){unset($this->pos1[$user]);}
						if(isset($this->pos2[$user])){unset($this->pos2[$user]);}
						break; 
					case "pos1": 
					case "1": 
						$x =round($issuer->entity->x -0.5); 
						$y =round($issuer->entity->y); 
						$z =round($issuer->entity->z -0.5); 
						$this->pos1[$user] =array($x, $y, $z); 
						$this->level[$user][0] =$issuer->level->getName(); 
						$output .= "[AreaProtector] First position set to (".$this->pos1[$user][0].", ".$this->pos1[$user][1].", ".$this->pos1[$user][2].")"; 
						break; 
					case "pos2": 
					case "2": 
						$x =round($issuer->entity->x -0.5);
						$y =round($issuer->entity->y); 
						$z =round($issuer->entity->z -0.5); 
						$this->pos2[$user] =array($x, $y, $z); 
						$this->level[$user][1] =$issuer->level->getName(); 
						$output .= "[AreaProtector] Second position set to (".$this->pos2[$user][0].", ".$this->pos2[$user][1].", ".$this->pos2[$user][2].")"; 
						break; 
					default: 
						$this->getAreas($issuer, $output);
				}
				break; 
			case "unprotect": 
				$targetProtectID =(string) trim(array_shift($params)); 
				if (count($this->config[$user]) == 0) {
					$output .= "You have no private area."; 
					break;
				}
				if (empty($world)) {
					if(isset($this->config[$user][$issuer->level->getName()][$targetProtectID])){
						unset($this->config[$user][$issuer->level->getName()][$targetProtectID]);
						$this->writeConfig($this->config);
						$output .= "Lifted the protection."; 
						return("Lifted the protection. \nProtection ID: " . $targetProtectID);
					}else{
						return("Target Protection ID doesn't exist. ");
					}
					$output .= "Usage: /unprotect <ProtectID>"; $this->getAreas($issuer, $output); 
					break;
				}
				if (!isset($this->config[$user][$world])) {
					$output .= "You don't have a private area in \"".$world."\"."; 
					$this->getAreas($issuer, $output); 
					break;
				}
				$protectID = $this->getProtectID($issuer, $issuer->level->getName());
				if($protectID == -1){
					return("You are not in a protected area. ");
				}
				if(isset($this->config[$user][$issuer->level->getName()][$protectID])){
					unset($this->config[$user][$issuer->level->getName()][$protectID]);
				}
				$this->writeConfig($this->config); 
				$output .= "Lifted the protection.\nProtection ID: " . $protectID; 
				$this->getAreas($issuer, $output); 
				break;
		}
}elseif ($issuer == "console") {switch($cmd){
	case "protect": 
		$output .= "======Private Areas List======";
		break; 
	case "unprotect": $user =array_shift($params); $world =array_shift($params); if (empty($user) || empty($world)) {$output .= "Usage: /unprotect <area owner> <world name>"; break;
}
if (!isset($this->config[$user][$world])) {$output .= "His area does'nt exist in \"".$world."\"!"; break;
}
if (!$this->config[$user][$world]['protect']) {$output .= "His area is not protected."; break;
}
$this->config[$user][$world]['protect'] =false; $this->writeConfig($this->config); $output .= "Lifted the protection.
"; break;
}
}
return $output;
}
public function b7ds5g4(&$data, $event){
switch ($event) {
	case "player.block.touch":
		$block = $data["target"];
		$x =$block->x; 
		$y =$block->y; 
		$z =$block->z; 
		foreach ($this->config as $name => $wlds) {
			if ($name == $data['player']->iusername) {
				continue;
			}
			foreach ($wlds as $wldName => $wld) {
				if($wldName != $block->level->getName()){
					continue;
				}
				foreach($wld as $config)
					if($config['min'][0] <= $x && $x <= $config['max'][0]) {
						if ($config['min'][1] <= $y && $y <= $config['max'][1]) {
							if ($config['min'][2] <= $z && $z <= $config['max'][2]) {
								$data['player']->sendChat("This is ".$name."'s protected area."); 
								return false;
							}
						}
					}
				}
			}
		}
		return;
		break; 
	case 'player.block.break': 
		$block =$data['target']; 
		$x =$block->x; 
		$y =$block->y; 
		$z =$block->z; 
		foreach ($this->config as $name => $wlds) {
			if ($name == $data['player']->iusername) {
				continue;
			}
			foreach ($wlds as $wldName => $wld) {
				if($wldName != $block->level->getName()){
					continue;
				}
				foreach ($wld as $config) {
					if($config['min'][0] <= $x && $x <= $config['max'][0]) {
						if ($config['min'][1] <= $y && $y <= $config['max'][1]) {
							if ($config['min'][2] <= $z && $z <= $config['max'][2]) {
								$data['player']->sendChat("This is ".$name."'s protected area."); 
								return false;
							}
						}
					}
				}
			}
		}
		return;
		break; 
	case 'player.block.place': 
		$block =$data['block']; 
		$x =$block->x; 
		$y =$block->y; 
		$z =$block->z; 
		foreach ($this->config as $name => $wlds) {
			if ($name == $data['player']->iusername) {
				continue;
			}
			foreach ($wlds as $wldName => $wld) {
				if($wldName != $block->level->getName()){
					continue;
				}
				foreach($wld as $config)
					if($config['min'][0] <= $x && $x <= $config['max'][0]) {
						if ($config['min'][1] <= $y && $y <= $config['max'][1]) {
							if ($config['min'][2] <= $z && $z <= $config['max'][2]) {
								$data['player']->sendChat("This is ".$name."'s protected area."); 
								return false;
							}
						}
					}
				}
			}
		}
		return;
		break;
	}
}
public function getAreas($player, &$output) {
	$cnt =(int) 0; $pids =""; 
	if(!(isset($this->config[$player->iusername][$player->level->getName()]))){return("You don't have protections in this world. ");}
	foreach ($this->config[$player->iusername][$player->level->getName()] as $pid => $data) {
		$cnt++; $pids .= $pid."  ";
	}
	$output .= "Your private areas in this world: \n >[".$cnt." areas]<\n"; 
	$output .= $pids;
}

public function createConfig() {$this->path =$this->api->plugin->createConfig($this, array()); $config =$this->api->plugin->readYAML($this->path."config.yml"); $this->config =$config;
}
public function writeConfig($data) {$this->api->plugin->writeYAML($this->path."config.yml", $data);
}


public function getProtectID($player, $world){
		if(isset($this->config[$player->iusername][$world])){
			foreach ($this->config[$player][$world] as $config) {
				$x =$player->entity->x;
				$y =$player->entity->y; 
				$z =$player->entity->z; 
				if($config['min'][0] <= $x && $x <= $config['max'][0]) {
					if ($config['min'][1] <= $y && $y <= $config['max'][1]) {
						if ($config['min'][2] <= $z && $z <= $config['max'][2]) {
							return $w;
						}
					}
				}
			}
		}
		return(-1);
}

}

?>
