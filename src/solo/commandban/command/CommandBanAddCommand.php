<?php

namespace solo\commandban\command;

use pocketmine\command\CommandSender;
use solo\commandban\CommandBan;
use solo\commandban\CommandBanCommand;

class CommandBanAddCommand extends CommandBanCommand{

  private $owner;

  public function __construct(CommandBan $owner){
    parent::__construct("명령어밴추가", "명령어를 차단합니다.", "/명령어밴추가 <명령어...>");
    $this->setPermission("commandban.command.add");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(CommandBan::$prefix . "이 명령을 사용할 권한이 없습니다.");
      return true;
    }
    if(empty($args)){
      $sender->sendMessage(CommandBan::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      return true;
    }
    $commandText = implode(" ", $args);

    $this->owner->addCommandBan($commandText);

    $this->owner->save();
    $sender->sendMessage(CommandBan::$prefix . "명령어를 차단하였습니다 : " . $commandText);
    return true;
  }
}
