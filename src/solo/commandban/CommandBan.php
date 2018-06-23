<?php

namespace solo\commandban;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class CommandBan extends PluginBase implements Listener{

  public static $prefix = "§b§l[CommandBan] §r§7";

  private $banList = [];

  public function onEnable(){
    @mkdir($this->getDataFolder());

    if(file_exists($this->getDataFolder() . "BanList.json")){
      $this->banList = json_decode(file_get_contents($this->getDataFolder() . "BanList.json"), true);
    }

    foreach([
      "CommandBanAddCommand", "CommandBanListCommand", "CommandBanRemoveCommand"
    ] as $class){
      $class = "\\solo\\commandban\\command\\" . $class;
      $this->getServer()->getCommandMap()->register("commandban", new $class($this));
    }
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }

  public function save(){
    file_put_contents($this->getDataFolder() . "BanList.json", json_encode($this->banList));
  }

  public function addCommandBan(string $commandText){
    $currentDimension = &$this->banList;
    foreach(explode(" ", $commandText) as $arg){
      if(!isset($currentDimension[$arg])){
        $currentDimension[$arg] = [];
      }
      $currentDimension = &$currentDimension[$arg];
    }
    $currentDimension["*"] = [];
  }

  public function removeCommandBan(string $commandText){
    $currentDimension = &$this->banList;
    $args = explode(" ", $commandText);
    $key_end = count($args) - 1;
    foreach($args as $key_current => $arg){
      if(!isset($currentDimension[$arg])){
        return false;
      }
      if($key_current !== $key_end){
        $currentDimension = &$currentDimension[$arg];
        continue;
      }
      if(isset($currentDimension[$arg]["*"])){
        unset($currentDimension[$arg]);
      }else{
        return false;
      }
    }
    return true;
  }

  public function getCommandBanList(){
    return $this->banList;
  }

  public function checkCommand(string $commandText){
    $currentDimension = &$this->banList;
    foreach(explode(" ", $commandText) as $arg){
      if(isset($currentDimension[$arg])){
        if(isset($currentDimension[$arg]["*"])){
          return false;
        }
      }else{
        break;
      }
      $currentDimension = &$currentDimension[$arg];
    }
    return true;
  }

  /**
   * @priority HIGH
   *
   * @ignoreCancelled true
   */
  public function handlePlayerCommandPreprocess(PlayerCommandPreprocessEvent $event){
    if(
      substr($event->getMessage(), 0, 1) === '/'
      && !$event->getPlayer()->hasPermission("commandban.ignore")
      && $this->checkCommand(substr($event->getMessage(), 1)) === false
    ){
      $event->getPlayer()->sendMessage(CommandBan::$prefix . "해당 명령어는 사용할 수 없습니다.");
      $event->setCancelled();
    }
  }
}
