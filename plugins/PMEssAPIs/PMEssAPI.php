<?php

/* 

PocketEssentials API ( Only for Non-Commercial Use )
By Kevin Wang
From China

Skype: kvwang98
Twitter: KevinWang_China
Youtube: http://www.youtube.com/VanishedKevin
E-Mail: kevin@cnkvha.com

*/

class PMEssAPI{
	private $server;
	private $api;
	function __construct(){
		$this->server = ServerAPI::request();
		$this->api = $this->server->api;
	}
	
	public function init(){
	}
	
	public function switchVanish($player, $silenced = false){
		if(!($player instanceof Player)){
			$p = $this->server->api->player->get($player);
			if($p != false){
				return($this->switchVanish($p));
			}else{
				return(false);
			}
		}
		if(!(isset($this->server->api->session->sessions[$player->CID]["isVanished"]))){
			$this->server->api->session->sessions[$player->CID]["isVanished"] = false;
		}
		if($this->server->api->session->sessions[$player->CID]["isVanished"] == false){
			foreach($player->level->players as $p)
			{
				if($player->eid != $p->eid){
					$p->dataPacket(MC_REMOVE_ENTITY, array(
					"eid" => $player->eid
					));
				}
			}
			$this->server->api->session->sessions[$player->CID]["isVanished"] = true;
			if($silenced == false){
				$player->sendChat("You are now VANISHED! ");
			}
		}else{
		/*
			
		*/
			$this->server->api->session->sessions[$player->CID]["isVanished"] = false;
			
			if($this->api->session->sessions[$player->CID]["dPState"] == true){
				foreach($player->entity->level->players as $p){
					if($player->CID == $p->CID){continue;}
					PMEssCore::recreateDPEntity($p, $player);
				}
			}
			if($this->api->session->sessions[$player->CID]["dMState"]){
				foreach($player->entity->level->players as $p){
					if($player->CID == $p->CID){continue;}
					PMEssCore::recreateEntityToMob($p, $this->api->session->sessions[$player->CID]["dMData"], $player);
				}
			}
			if($this->api->session->sessions[$player->CID]["dEState"]){
				if($this->api->session->sessions[$player->CID]["dEType"] == 1){
					foreach($player->entity->level->players as $p){
						if($player->CID == $p->CID){continue;}
						PMEssCore::recreatePTNTEntity($p, $player);
					}
				}elseif($this->api->session->sessions[$player->CID]["dEType"] == 2){
					foreach($player->entity->level->players as $p){
						if($player->CID == $p->CID){continue;}
						PMEssCore::recreateBlockEntity($p, $player, $this->api->session->sessions[$player->CID]["dEBlockID"]);
					}
				}
			}
			if($this->api->session->sessions[$player->CID]["dPState"] == false and $this->api->session->sessions[$player->CID]["dMState"] == false and $this->api->session->sessions[$player->CID]["dEState"] == false){
				if($this->server->api->session->sessions[$player->CID]["dPState"]){
					$un = $this->server->api->session->sessions[$player->CID]["dPUsername"];
				}else{
					$un = $player->username;
				}
				
				foreach($player->level->players as $p){
					if($player->eid != $p->eid){
						$p->dataPacket(MC_ADD_PLAYER, array(
							"clientID" => 0,
							"username" => $un,
							"eid" => $player->eid,
							"x" => $player->entity->x,
							"y" => $player->entity->y,
							"z" => $player->entity->z,
							"yaw" => 0,
							"pitch" => 0,
							"unknown1" => 0,
							"unknown2" => 0,
							"metadata" => $player->entity->getMetadata()));
					}
				}
			}
		
			if($silenced == false){
				$player->sendChat("You are visible again! ");
			}
		}
		return(true);
	}
	
	public function disguiseAsBlock($player, $blockID = 20){
		if(!($player instanceof Player)){
			$p = $this->server->api->player->get($player);
			if($p != false){
				return($this->disguiseAsBlock($p, $blockID));
			}else{
				return(false);
			}
		}
		//Undisguise
		if($this->api->session->sessions[$player->CID]["dPState"] == true){
			$this->api->session->sessions[$player->CID]["dPState"] = false;
			$this->api->session->sessions[$player->CID]["dPUsername"] = "";
		}
		if($this->api->session->sessions[$player->CID]["dMState"] == true){
			$this->api->session->sessions[$player->CID]["dMState"] = false;
			$this->api->session->sessions[$player->CID]["dMData"] = 0x00;
		}
		if(isset($this->dEData[$player->CID])){unset($this->dEData[$player->CID]);}
		$this->api->session->sessions[$player->CID]["dEState"] = true;
		$this->api->session->sessions[$player->CID]["dEType"] = 2;
		$this->api->session->sessions[$player->CID]["dEBlockID"] = (int) $blockID;
		foreach($player->level->players as $p){
			if($p->eid != $player->eid){
				$p->dataPacket(MC_REMOVE_ENTITY, array(
					"eid" => $player->eid
					));
				$p->dataPacket(MC_ADD_ENTITY, array(
					"eid" => $player->eid,
					"type" => FALLING_SAND,
					"x" => $player->entity->x,
					"y" => $player->entity->y,
					"z" => $player->entity->z,
					"did" => -((int)$blockID),
				));
				$p->dataPacket(MC_SET_ENTITY_MOTION, array(
					"eid" => $player->eid,
					"speedX" => 0,
					"speedY" => 64,
					"speedZ" => 0
				));
			}
		}
	}
	
	public function undisguise($player){
		if($this->api->session->sessions[$player->CID]["dPState"] == true){
			$this->api->session->sessions[$player->CID]["dPState"] = false;
			$this->api->session->sessions[$player->CID]["dPUsername"] = "";
		}
		if($this->api->session->sessions[$player->CID]["dMState"] == true){
			$this->api->session->sessions[$player->CID]["dMState"] = false;
			$this->api->session->sessions[$player->CID]["dMData"] = 0x00;
		}
		if(isset($this->dEData[$player->CID])){unset($this->dEData[$player->CID]);}
		$this->api->session->sessions[$player->CID]["dEState"] = false;
		$this->api->session->sessions[$player->CID]["dEType"] = 0;
		$this->api->session->sessions[$player->CID]["dEBlockID"] = -1;
		foreach($player->level->players as $p){
			if($p->eid != $player->eid){
				$p->dataPacket(MC_REMOVE_ENTITY, array(
					"eid" => $player->eid
					));
				$p->dataPacket(MC_ADD_PLAYER, array(
					"clientID" => 0,
					"username" => $player->username,
					"eid" => $player->eid,
					"x" => $player->entity->x,
					"y" => $player->entity->y,
					"z" => $player->entity->z,
					"yaw" => 0,
					"pitch" => 0,
					"unknown1" => 0,
					"unknown2" => 0,
					"metadata" => $player->entity->getMetadata())
					);
			}
		}
	}
	
	public function SendBlockUpdateRAW($pos, $block){
		if(!($pos instanceof Position) or !($block instanceof Block)){return(false);}
		$this->server->api->player->broadcastPacket($this->server->api->player->getAll($pos->level), MC_UPDATE_BLOCK, array(
			"x" => $block->x,
			"y" => $block->y,
			"z" => $block->z,
			"block" => $block->getID(),
			"meta" => $block->getMetadata()
		));
		return(true);
	}
	
}
?> 
