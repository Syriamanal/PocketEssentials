<?php
/*
__PocketMine Plugin__
name=PMEssentials-Signs
version=4.1.7-Alpha
author=Kevin Wang
class=PMEssSigns
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

class PMEssSigns implements Plugin{
	private $server;
	private $api;
	private $signBrokenMessage = "This sign is broken! ";
	private $noPermissionMessage = "You don't have permission to use this sign. ";
	public function __construct(ServerAPI $api, $server = false){
		$this->server = ServerAPI::request();
		$this->api = $api;
	}
	
	public function init(){
        $this->api->addHandler("player.block.touch", array($this, "handleTapEvents"), 8); 
		$this->api->addHandler("tile.update", array($this, "handleSignTextChangeEvents"), 8);
		$this->api->schedule(40, array($this, "timerUpdateSign"), array(), true);
	}
	
	public function __destruct(){
	}
	
    public function handleTapEvents(&$data, $event){
        if(!($data["player"] instanceof Player)){return(false);}
		if(!($this->api->perm->checkPerm($data["player"]->username, "pmess.signs.use"))){
			$data["player"]->sendChat($this->noPermissionMessage);
			return;
		}
        $t = $this->api->tile->get(new Position($data["target"]->x, $data["target"]->y, $data["target"]->z, $data["target"]->level));
        if(!($t instanceof Tile)){return;}
        if($t->class != TILE_SIGN){return;}
        if(strtolower($t->data["Text1"]) == "[warp]"){
            if($t->data["Text2"] == ""){
				$data["player"]->sendChat($this->signBrokenMessage);
				return;
			}
			$pos = $this->api->warp->getWarp($t->data["Text2"]);
			if($pos instanceof Position){
				$data["player"]->teleport($pos);
			}else{
				$data["player"]->sendChat($this->signBrokenMessage);
			}
			return;
        }
		if(strtolower($t->data["Text1"]) == "[world]"){
			$lv = $this->api->level->get($t->data["Text2"]);
			if($lv instanceof Level){
				$data["player"]->teleport($lv->getSafeSpawn());
			}else{
				$data["player"]->sendChat($this->signBrokenMessage);
			}
			return;
		}
		if(strtolower($t->data["Text1"]) == "[free]"){
			$quantity = (int) $t->data["Text3"];
			if(($t->data["Text3"] != "") and ((int) $quantity < 1)){
				$data["player"]->sendChat($this->signBrokenMessage);
				return;
			}
			if($t->data["Text3"] == ""){
				$quantity = 1;
			}
			$this->api->console->run("give " . $data["player"]->username . " " . $t->data["Text2"] . " " . $quantity);
			$data["player"]->sendChat("Items were given. ");
			return;
		}
		if(strtolower($t->data["Text1"]) == "[spawn]"){
			$data["player"]->teleport($this->api->level->getDefault()->getSafeSpawn());
			return;
		}
    }
	
	public function handleSignTextChangeEvents(&$data, $event){
		if(!($data instanceof Tile)){return;}
		if($data->class != TILE_SIGN){return;}
		if($data->data["Text1"] != "[warp]" and $data->data["Text1"] != "[free]" and $data->data["Text1"] != "[world]" and $data->data["Text1"] != "[spawn]"){
			return;
		}
		if($this->api->perm->checkPerm($data->data["creator"], "pmess.signs.create")){return;}
		$data->data["Text1"]="You don't have";
		$data->data["Text2"]="have permission";
		$data->data["Text3"]="to create a";
		$data->data["Text4"]="PMEss Sign.";
		$this->api->tile->spawnToAll($data);
	}
	
	public function checkValid($s){
		if(preg_match("/^[0-9a-zA-Z\_]*$/",$s)){
			return(true);
		}else{
			return(false);
		}
	}
	
	public function timerUpdateSign(){
		$tiles = array();
		$l = $this->server->query("SELECT ID FROM tiles WHERE class = '".TILE_SIGN."';");
		if($l !== false and $l !== true){
			while(($t = $l->fetchArray(SQLITE3_ASSOC)) !== false){
				$t = $this->api->tile->getByID($t["ID"]);
				if($t instanceof Tile){
					$tiles[$t->id] = $t;
				}
			}
		}
		foreach($tiles as $tile){
			if(strtolower($tile->data["Text1"]) != "[world]"){continue;}
			$lv = $this->api->level->get($tile->data["Text2"]);
			if(!($lv instanceof Level)){continue;}
			$nbt = new NBT();
			$nbt->write(chr(NBT::TAG_COMPOUND)."\x00\x00");
			$nbt->write(chr(NBT::TAG_STRING));
			$nbt->writeTAG_String("Text1");
			$nbt->writeTAG_String("Tap to go to");
			$nbt->write(chr(NBT::TAG_STRING));
			$nbt->writeTAG_String("Text2");
			$nbt->writeTAG_String($tile->data["Text2"]);
			$nbt->write(chr(NBT::TAG_STRING));
			$nbt->writeTAG_String("Text3");
			$nbt->writeTAG_String("Players:");
			$nbt->write(chr(NBT::TAG_STRING));
			$nbt->writeTAG_String("Text4");
			$nbt->writeTAG_String(count($this->api->player->getAll($lv)));
			$nbt->write(chr(NBT::TAG_STRING));
			$nbt->writeTAG_String("id");
			$nbt->writeTAG_String(TILE_SIGN);
			$nbt->write(chr(NBT::TAG_INT));
			$nbt->writeTAG_String("x");
			$nbt->writeTAG_Int((int) $tile->x);
			$nbt->write(chr(NBT::TAG_INT));
			$nbt->writeTAG_String("y");
			$nbt->writeTAG_Int((int) $tile->y);
			$nbt->write(chr(NBT::TAG_INT));
			$nbt->writeTAG_String("z");
			$nbt->writeTAG_Int((int) $tile->z);
			$nbt->write(chr(NBT::TAG_END));				
			$this->api->player->broadcastPacket($this->api->player->getAll($tile->level), MC_ENTITY_DATA, array(
					"x" => $tile->x,
					"y" => $tile->y,
					"z" => $tile->z,
					"namedtag" => $nbt->binary,
				));
		}
	}
	
}
?>
