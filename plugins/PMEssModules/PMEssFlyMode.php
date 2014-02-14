<?php

/*
__PocketMine Plugin__
name=PocketEssentials-FlyMode
version=5.0.0-Beta
author=Kevin Wang
class=KVFlyMode
apiversion=11,12
*/




class KVFlyMode implements Plugin{
	private $api;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
		$this->api->addHandler("player.move", array($this, "handler"), 10);
		$this->api->addHandler("player.quit", array($this, "handler"), 10);
		$this->api->addHandler("entity.health.change", array($this, "handler"), 20);
		$this->api->addHandler("player.block.break", array($this, "handler"), 20);
		$this->api->addHandler("player.block.break.invalid", array($this, "handler"), 20);
		$this->api->addHandler("player.block.place.invalid", array($this, "handler"), 20);
		$this->api->console->register("fly", "Let you fly by jumping. ", array($this, "command"));
	}
	
	public function __destruct(){
	}
	
	public function handler($data, $event){
		switch($event){
			case "entity.health.change":
				if(!($data["entity"]->player instanceof Player)){return;}
				if(isset($this->api->session->sessions[$data["entity"]->player->CID]["FlyModeData"])){
                    if(is_array($this->api->session->sessions[$data["entity"]->player->CID]["FlyModeData"])){
                        return(false);
                    }
				}
				break;
			case "player.move":
				if(isset($this->api->session->sessions[$data->player->CID]["FlyModeData"])){
					$this->doBlockActions($data);
				}
				break;
			case "player.quit":
				unset($this->api->session->sessions[$data->CID]["FlyModeData"]);
				break;
			case "player.block.place.invalid":
				if(isset($this->api->session->sessions[$data["player"]->CID]["FlyModeData"])){
                    if(isset($this->api->session->sessions[$data["player"]->CID]["FlyModeData"]["blocks"][$data["target"]->x.".".$data["target"]->y.".".$data["target"]->z])){
						return true;
					}
				}
				break;
			case "player.block.break.invalid":
			case "player.block.break":
				if(isset($this->api->session->sessions[$data["player"]->CID]["FlyModeData"])){
					if(isset($this->api->session->sessions[$data["player"]->CID]["FlyModeData"]["blocks"][$data["target"]->x.".".$data["target"]->y.".".$data["target"]->z])){
						unset($this->api->session->sessions[$data["player"]->CID]["FlyModeData"]["blocks"][$data["target"]->x.".".$data["target"]->y.".".$data["target"]->z]);
					}
				}
				break;
		}
	}
	
	private function doBlockActions(Entity $player){
					$session =& $this->api->session->sessions[$player->player->CID]["FlyModeData"];
					$startX = (int) ($player->x - ($session["size"] - 1) / 2);
					$y = ((int) $player->y);
					if($player->pitch > 75){
						--$y;
					}
					if($player->pitch < -24){
						++$y;
					}
					$startZ = (int) ($player->z - ($session["size"] - 1) / 2);
					$endX = $startX + $session["size"];
					$endZ = $startZ + $session["size"];
					$newBlocks = array();
					for($x = $startX; $x < $endX; ++$x){
						for($z = $startZ; $z < $endZ; ++$z){
							$i = "$x.$y.$z";
							if(isset($session["blocks"][$i])){
								$newBlocks[$i] = $session["blocks"][$i];
								unset($session["blocks"][$i]);
							}else{
								$newBlocks[$i] = $player->level->getBlock(new Vector3($x, $y, $z));
								if($newBlocks[$i]->getID() === AIR){
									$session["blocks"][$i] = BlockAPI::get(9);
									$session["blocks"][$i]->position(new Position($x, $y, $z, $player->level));
								}
							}
						}
					}

					foreach($session["blocks"] as $i => $block){
						$index = array_map("intval", explode(".", $i));
                        $pk = new UpdateBlockPacket();
                        $pk->x = $index[0];
						$pk->y = $index[1];
						$pk->z = $index[2];
						$pk->block = $block->getID();
						$pk->meta = $block->getMetadata();
						foreach($this->api->player->getAll($player->level) as $p){
							$p->dataPacket($pk);
						}
					}
					$this->api->session->sessions[$player->player->CID]["FlyModeData"]["blocks"] = $newBlocks;
	}
	
	public function command($cmd, $params, $issuer, $alias){
		$output = "";
		if(!($issuer instanceof Player)){					
			$output .= "Please run this command in-game.\n";
			return $output;
		}
		$size = 4;
		
		if(!isset($this->api->session->sessions[$issuer->CID]["FlyModeData"])){
			if($issuer->gamemode == 0x01){
				return("You can't enable fly mode in creative mode. ");
			}
			$this->api->session->sessions[$issuer->CID]["FlyModeData"] = array(
				"size" => $size,
				"blocks" => array(),
			);
			$output .= "Enabled fly mode for the user. \n";
			$this->doBlockActions($issuer->entity);
		}else{
			foreach($this->api->session->sessions[$issuer->CID]["FlyModeData"]["blocks"] as $i => $block){
				$index = array_map("intval", explode(".", $i));
                $pk = new UpdateBlockPacket();
                $pk->x = $index[0];
                $pk->y = $index[1];
                $pk->z = $index[2];
                $pk->block = $block->getID();
                $pk->meta = $block->getMetadata();
				foreach($this->api->player->getAll($issuer->level) as $p){
					$p->dataPacket($pk);
				}
			}
			unset($this->api->session->sessions[$issuer->CID]["FlyModeData"]);
			$output .= "Disabled fly mode for the user. \n";
		}
		return $output;
	}
}