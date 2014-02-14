<?php
/*
__PocketMine Plugin__
name=PocketEssentials-SeprateChat
version=5.0.0-Beta
author=Kevin Wang
class=PMEssSeprateChat
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
ChatGroups{
    [InvName]{
        [0]{
            type(Number) = World Name Type. 0=Match All, 1=Start With, 2=End With
            name(String) = World Name, format depends on Type
        }
    }
}
*/

class PMEssSeprateChat implements Plugin{
	private $api;
    private $configData;
    private $defaultConfig = array("ChatGroups" => array("default" => array(array("type" => 0, "name" => "world"))));
    
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
        //Get the config
        $cfg = new Config($this->api->plugin->configPath($this)."Config.json", CONFIG_JSON, $this->defaultConfig);
        $this->configData = $cfg->get("ChatGroups");
        unset($cfg);
        //Register the handler
        $this->api->addHandler("pmess.chat.sepratehandler", array($this, "handleSendSeprateChat"), 5);
	}
	

    public function handleSendSeprateChat(&$data, $event){
        if(!($data["player"] instanceof Player)){return(false);}
        $groupName = $this->getChatGroup($data["player"]->level->getName());
        if($groupName == "__global__"){return(false);}
        $allUsers = array();
        foreach($this->configData[$groupName] as $wID => $wData){
            if($wData["type"] == 0){
                $level = $this->api->level->get($wData["name"]);
                if($level instanceof Level){
                    foreach($level->players as $p){
                        $allUsers[] = $p->username;
                    }
                }
            }elseif($wData["type"] == 1){
                $levels = array();
                foreach($this->api->level->getAll() as $level){
                    if($this->api->utils->startWith($level->getName(), $wData["name"])){
                        foreach($level->players as $p){
                            $allUsers[] = $p->username;
                        }
                    }
                }
            }elseif($wData["type"] == 2){
                $levels = array();
                foreach($this->api->level->getAll() as $level){
                    if($this->api->utils->endWith($level->getName(), $wData["name"])){
                        foreach($level->players as $p){
                            $allUsers[] = $p->username;
                        }
                    }
                }
            }
        }
        $this->api->chat->send(false, $data["fullmessage"], $allUsers);
        return(true);
    }
    
    
    
    public function getChatGroup($wName){
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
        return("__global__");
    }
    


    public function __destruct(){
    }
	
}
?>
