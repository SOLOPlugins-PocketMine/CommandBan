<?php

namespace solo\commandban\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use solo\commandban\CommandBan;

class CommandBanListCommand extends Command{

  private $owner;

  public function __construct(CommandBan $owner){
    parent::__construct("명령어밴목록", "차단된 명령어의 목록을 확인합니다.", "/명령어밴목록 [페이지]");
    $this->setPermission("commandban.command.list");

    $this->owner = $owner;
  }

  public function execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(CommandBan::$prefix . "이 명령을 사용할 권한이 없습니다.");
      return true;
    }
    $banList = $this->makeListFromTree($this->owner->getCommandBanList());

    $countPerPage = 5;

    $maxPage = ceil(count($banList) / $countPerPage);
    $page = 1;
    if(!empty($args) && preg_match("/[0-9]+/", $args[0])){
      $page = max(1, min($maxPage, intval($args[0])));
    }

    $sender->sendMessage("§l==========[ 차단된 명령어 목록 (전체 " . $maxPage . "페이지 중 " . $page . "페이지) ]==========§r");
    for($i = ($page - 1) * $countPerPage; $i < $page * $countPerPage; $i++){
      if(!isset($banList[$i])){
        break;
      }
      $sender->sendMessage("[" . $i . "] §7" . $banList[$i]);
    }
    return true;
  }

  private function makeListFromTree(array $tree){
    $list = [];
    foreach($tree as $arg => $subTree){
      if(!empty($subTree)){
        foreach($this->makeListFromTree($subTree) as $subArg){
          $list[] = $arg . " " . $subArg;
        }
      }else{
        $list[] = $arg;
      }
    }
    return $list;
  }
}
