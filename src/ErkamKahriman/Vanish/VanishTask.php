<?php

namespace ErkamKahriman\Vanish;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use ErkamKahriman\Vanish\Vanish;

class VanishTask extends Task {

    /** @var Vanish */
    private $plugin;

    public function __construct(Vanish $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick) : void {

        $vanished = Vanish::$vanished;

        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
            
            if($player->isOp()) return;
            
            foreach($vanished as $vanishedPlayer) {
                $player->hidePlayer($vanishedPlayer);
                $vanishedPlayer->getServer()->removePlayerListData($vanishedPlayer->getUniqueId());
            }
            
        }

    }
}
