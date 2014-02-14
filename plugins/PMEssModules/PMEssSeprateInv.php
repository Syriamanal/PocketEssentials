<?php
/*
__PocketMine Plugin__
name=PocketEssentials-SeprateInventory
version=5.0.0-Beta
author=Kevin Wang
class=PMEssSeprateInv
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

/*
InvGroups{
    [InvName]{
        [0]{
            type(Number) = World Name Type. 0=Match All, 1=Start With, 2=End With
            name(String) = World Name, format depends on Type
        }
    }
}
*/

class PMEssSeprateInv implements Plugin{
	private $api;
    private $configData;
    private $dirPlayers;
    private $defaultGroup;
    private $defaultConfig = array("InvGroups" => array("default" => array(array("type" => 0, "name" => "world"))), "DefaultGroup" => "default");
    
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
        //Initialize folder structure
        $this->dirPlayers = $this->api->plugin->configPath($this)."/Players";
		$this->api->file->SafeCreateFolder($this->dirPlayers);
        //Get the config
        $cfg = new Config($this->api->plugin->configPath($this)."Config.json", CONFIG_JSON, $this->defaultConfig);
        $this->configData = $cfg->get("InvGroups");
        $this->defaultGroup = $cfg->get("DefaultGroup");
        unset($cfg);
        $this->api->addHandler("player.teleport.level", array($this, "handlePlayerLevelChange"), 64); 
	}
	
    public function handlePlayerLevelChange(&$data, $event){
        if(!($data["player"] instanceof Player) or !($data["origin"] instanceof Level) or !($data["target"] instanceof Level)){return;}
        if($data["player"]->spawned == false){return;}
        $cfg = new Config($this->dirPlayers."/" . strtolower($data["player"]->username) . ".yml", CONFIG_YAML, array("Inventories" => array()));
        $invs = $cfg->get("Inventories");
        $invGroupOrigin = strtolower($this->getInvGroup($data["origin"]->getName()));
        $invGroupTarget = strtolower($this->getInvGroup($data["target"]->getName()));
        $invs[$invGroupOrigin] = array("Inventory" => $this->getInvConfig($data["player"]), "Armor" => $this->getArmorConfig($data["player"]));
        $cfg->set("Inventories", $invs);
        $cfg->save();
        unset($cfg);
        $newInv = array();
        $newArmor = array();
        if(!(isset($invs[$invGroupTarget]))){
            foreach($data["player"]->inventory as $slot => $item){
				$newInv[$slot] = BlockAPI::getItem(AIR, 0, 0);
			}
            foreach($data["player"]->armor as $slot => $item){
				$newArmor[$slot] = BlockAPI::getItem(AIR, 0, 0);
			}
        }else{
            foreach($invs[$invGroupTarget]["Inventory"] as $slot => $item){
				if(!is_array($item) or count($item) < 3){
					$item = array(AIR, 0, 0);
				}
				$newInv[$slot] = BlockAPI::getItem($item[0], $item[1], $item[2]);
			}
            foreach($invs[$invGroupTarget]["Armor"] as $slot => $item){
				$newArmor[$slot] = BlockAPI::getItem($item[0], $item[1], $item[0] === 0 ? 0:1);
			}
        }
        $data["player"]->inventory = $newInv;
        $data["player"]->armor = $newArmor;
        $data["player"]->sendInventory();
        $data["player"]->sendArmor();
    }
    
    
    
    
    public function getInvGroup($wName){
        $wName = strtolower($wName);
        //Search from type 0 to 2
        foreach($this->configData as $invName => $worlds){
            foreach($worlds as $wIndex => $wData){
                if($wData["type"] != 0){continue;}
                if($wName == strtolower($wData["name"])){
                    return($invName);
                }
            }
        }
        foreach($this->configData as $invName => $worlds){
            foreach($worlds as $wIndex => $wData){
                if($wData["type"] != 1){continue;}
                if($this->api->utils->startWith($wName, strtolower($wData["name"]))){
                    return($invName);
                }
            }
        }
        foreach($this->configData as $invName => $worlds){
            foreach($worlds as $wIndex => $wData){
                if($wData["type"] != 2){continue;}
                if($this->api->utils->endWith($wName, strtolower($wData["name"]))){
                    return($invName);
                }
            }
        }
        return($this->defaultGroup);
    }
    
    public function getInvConfig($p){
        if(!($p instanceof Player)){return(false);}
        $inv = array();			
		foreach($p->inventory as $slot => $item){
			if($item instanceof Item){
				if($slot < (($p->gamemode & 0x01) === 0 ? PLAYER_SURVIVAL_SLOTS:PLAYER_CREATIVE_SLOTS)){
					$inv[$slot] = array($item->getID(), $item->getMetadata(), $item->count);
				}
			}
		}
        return($inv);
    }
    
    public function getArmorConfig($p){
        if(!($p instanceof Player)){return(false);}
        $armor = array();
		foreach($p->armor as $slot => $item){
			if($item instanceof Item){
				$armor[$slot] = array($item->getID(), $item->getMetadata());
			}
		}
        return($armor);
    }
    

    public function __destruct(){
    }
	
}
?>
