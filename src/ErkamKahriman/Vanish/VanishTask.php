<?php

namespace ErkamKahriman\Vanish;

use pocketmine\scheduler\Task;
use pocketmine\Server;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

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
                
                $vanishedPlayer->addEffect(new EffectInstance(Effect::getEffect(Effect::INVISIBILITY), 20*3, 1, false));
                $vanishedPlayer->addEffect(new EffectInstance(Effect::getEffect(Effect::RESISTANCE), 20*3, 255, false));
            }
            
        }

    }
}
