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
            $packet = new RemovePlayerPacket();
            $packet->eid = $player->eid;
			foreach($player->level->players as $p)
			{
				if($player->eid != $p->eid){
					$p->dataPacket($packet);
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
				
                $packet = new AddPlayerPacket();
                $packet->clientID = 0;
                $packet->username = $un;
                $packet->eid = $player->eid;
                $packet->x = $player->entity->x;
                $packet->y = $player->entity->y;
                $packet->z = $player->entity->z;
                $packet->yaw = 0;
                $packet->pitch = 0;
                $packet->unknown1 = 0;
                $packet->unknown2 = 0;
                $packet->metadata = $player->entity->getMetadata();
				foreach($player->level->players as $p){
					if($player->eid != $p->eid){
						$p->dataPacket($packet);
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
        $packetRemove = new RemoveEntityPacket();
        $packetRemove->eid = $player->eid;
        $packetAdd = new AddEntityPacket();
        $packetAdd->eid = $player->eid;
        $packetAdd->type = FALLING_SAND;
        $packetAdd->x = $player->entity->x;
        $packetAdd->y = $player->entity->y;
        $packetAdd->z = $player->entity->z;
        $packetAdd->did = -((int)$blockID);
        $packetMotion = new SetEntityMotionPacket();
        $packetMotion->eid = $player->eid;
        $packetMotion->speedX = 0;
        $packetMotion->speedY = 64;
        $packetMotion->speedZ = 0;
		foreach($player->level->players as $p){
			if($p->eid != $player->eid){
				$p->dataPacket($packetRemove);
				$p->dataPacket($packetAdd);
				$p->dataPacket($packetMotion);
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
        $pkRemove = new RemoveEntityPacket();
        $pkRemove->eid = $player->eid;
        $pkAdd = new AddPlayerPacket();
        $pkAdd->clientID = 0;
		$pkAdd->username = $player->username;
		$pkAdd->eid = $player->eid;
		$pkAdd->x = $player->entity->x;
		$pkAdd->y = $player->entity->y;
		$pkAdd->z = $player->entity->z;
		$pkAdd->yaw = 0;
		$pkAdd->pitch = 0;
		$pkAdd->unknown1 = 0;
		$pkAdd->unknown2 = 0;
		$pkAdd->metadata = $player->entity->getMetadata();
		foreach($player->level->players as $p){
			if($p->eid != $player->eid){
				$p->dataPacket($pkRemove);
				$p->dataPacket($pkAdd);
			}
		}
	}
	
	public function sendBlockUpdateRAW($pos, $block){
		if(!($pos instanceof Position) or !($block instanceof Block)){return(false);}
        $pk = new UpdateBlockPacket();
		$pk->x = $pos->x;
		$pk->y = $pos->y;
		$pk->z = $pos->z;
		$pk->block = $block->getID();
		$pk->meta = $block->getMetadata();
		$this->server->api->player->broadcastPacket($this->server->api->player->getAll($pos->level), $pk);
		return(true);
	}
	
}
?> 
