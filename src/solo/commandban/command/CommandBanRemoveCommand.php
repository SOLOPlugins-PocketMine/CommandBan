<?php

namespace solo\commandban\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use solo\commandban\CommandBan;

class CommandBanRemoveCommand extends Command{

  private $owner;

  public function __construct(CommandBan $owner){
    parent::__construct("명령어밴삭제", "명령어의 차단을 해제합니다.", "/명령어밴삭제 <명령어...>");
    $this->setPermission("commandban.command.remove");

    $this->owner = $owner;
  }

  public function execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(CommandBan::$prefix . "이 명령을 사용할 권한이 없습니다.");
      return true;
    }
    if(empty($args)){
      $sender->sendMessage(CommandBan::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      return true;
    }
    $commandText = implode(" ", $args);

    $success = $this->owner->removeCommandBan($commandText);

    if($success){
      $this->owner->save();
      $sender->sendMessage(CommandBan::$prefix . "명령어를 차단해제하였습니다 : " . $commandText);
    }else{
      $sender->sendMessage(CommandBan::$prefix . "해당 명령어는 차단되어있지 않습니다 : " . $commandText);
    }
    return true;
  }
}
