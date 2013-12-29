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
				if(strtolower($player->eid) != strtolower($p->eid))
				{
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
			if($this->server->api->session->sessions[$player->CID]["dPState"]){
				$un = $this->server->api->session->sessions[$player->CID]["dPUsername"];
			}else{
				$un = $player->username;
			}
			
			foreach($player->level->players as $p){
				if(strtolower($player->eid) != strtolower($p->eid)){
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
		*/
			if($this->api->session->sessions[$player->CID]["isVanished"] == true){
				foreach($player->entity->level->players as $p){
					if($player->CID == $p->CID){continue;}
					$p->dataPacket(MC_REMOVE_ENTITY, array(
						"eid" => $player->entity->eid
						));
				}
			}elseif($this->api->session->sessions[$player->CID]["dPState"] == true){
				foreach($player->entity->level->players as $p){
					if($player->CID == $p->CID){continue;}
					PMEssCore::recreateDPEntity($p, $player);
				}
			}elseif($this->api->session->sessions[$player->CID]["dMState"]){
				foreach($player->entity->level->players as $p){
					if($player->CID == $p->CID){continue;}
					PMEssCore::recreateEntityToMob($p, $this->api->session->sessions[$player->CID]["dMData"], $player);
				}
			}elseif($this->api->session->sessions[$player->CID]["dEState"]){
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
		
		
		
			$this->server->api->session->sessions[$player->CID]["isVanished"] = false;
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
		foreach($issuer->level->players as $p){
			if($p->eid != $issuer->eid){
				PMEssCore::recreateBlockEntity($p, $player, (int) $blockID);
			}
		}
	}
	
}
?> 
