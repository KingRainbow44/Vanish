<?php

namespace ErkamKahriman\Vanish;

use pocketmine\event\player\{PlayerJoinEvent, PlayerLoginEvent, PlayerQuitEvent};
use pocketmine\plugin\PluginBase;
use pocketmine\entity\{Effect, EffectInstance};
use pocketmine\{Player, Server};
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;
use pocketmine\command\{Command, CommandSender};

class Vanish extends PluginBase implements Listener {

    const PREFIX = C::BLUE . "§7[" . C::GRAY . "§aSuper§6Vanish§7]" . C::RESET;

    private static $instance;

    public $vanish = [];

    /** @var array|Player[] */
    public static $vanished = [];

    public function onEnable() {
        self::$instance = $this;

        $this->saveResource("config.yml");
        $this->getScheduler()->scheduleRepeatingTask(new VanishTask(), 20);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(C::GREEN . "Plugin enabled.");
    }

    /**
     * @return static
     */
    public static function getInstance() : self {
        return self::$instance;
    }

    /**
     * @param CommandSender $sender
     * @param Command $cmd
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool
    {
        switch($cmd->getLabel()) {
            case "vanish":
                switch(isset(self::$vanished[$sender->getName()])) {
                    case false:
                        if($sender->hasPermission("supervanish.use")) {
                            self::$vanished[$sender->getName()] = $sender;
                            $sender->sendMessage("§a" . "You have been vanished.");
                            $sender->sendMessage("§e" . "Note: You will be vanished until you use '/vanish' or until the server reboots.");
                        }else{
                            $sender->sendMessage("§c" . "Unknown command. Try /help for a list of commands");
                        }
                        break;
                    case true:
                        if($sender->hasPermission("supervanish.use")) {
                            unset(self::$vanished[$sender->getName()]);
                            $sender->sendMessage("§c" . "You have been unvanished.");
                            
                            foreach(Server::getInstance()->getOnlinePlayers() as $player) {
                                assert($sender instanceof Player);
                                $player->showPlayer($sender);
                            }
                            
                        }else{
                            $sender->sendMessage("§c" . "Unknown command. Try /help for a list of commands");
                        }
                        break;
                }
                break;
        }

        return true;
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();

        if(isset(self::$vanished[$name])) {
            $event->setJoinMessage(null);
        }
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();

        if(isset(self::$vanished[$name])) {
            $event->setQuitMessage(null);
        }
    }

    public function onDisable() {
        $this->getLogger()->info(C::RED . "Plugin disabled.");
    }

}
