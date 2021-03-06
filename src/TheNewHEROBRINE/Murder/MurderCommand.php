<?php

namespace TheNewHEROBRINE\Murder;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class MurderCommand extends Command implements PluginIdentifiableCommand {

    private $plugin;

    /**
     * MurderCommand constructor.
     * @param MurderMain $plugin
     */
    public function __construct(MurderMain $plugin) {
        parent::__construct("murder", "Murder minigame main command", "/murder join {arena}|quit|setspawns {slots}", ["mdr"]);
        $this->plugin = $plugin;
    }

    /**
     * @return MurderMain
     */
    public function getPlugin(): MurderMain {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($sender instanceof Player) {
            if (isset($args[0])) {
                switch (array_shift($args)) {
                    case "join":
                        if ($arena = $this->getPlugin()->getArenaByName($args[0])) {
                            if (!$this->getPlugin()->getServer()->isLevelLoaded($arena));
                                $this->getPlugin()->getServer()->loadLevel($arena);
                            $arena->join($sender);
                        } else {
                            $this->getPlugin()->sendMessage(TextFormat::RED . "L'arena $args[0] non esiste!", $sender);
                        }
                        break;
                    case "quit":
                        if ($arena = $this->getPlugin()->getArenaByPlayer($sender))
                            $arena->quit($sender);
                        else
                            $this->getPlugin()->sendMessage(TextFormat::RED . "Non sei una partita di Murder!", $sender);
                        break;
                    case "setspawns":
                        $world = $sender->getLevel()->getName();
                        $name = $sender->getName();
                        if ($sender->hasPermission("murder.command.setspawns"))
                            if (isset($args[0]) && is_numeric($args[0])) {
                                $this->getPlugin()->getListener()->setspawns[$name][$world] = (int)$args[0];
                                $this->getPlugin()->getArenasCfg()->remove($world);
                                $this->getPlugin()->sendMessage("§eSettaggio di§f $args[0] §espawn per il mondo§f {$sender->getLevel()->getName()} §einiziato", $sender);
                            }
                        break;
                }
            }
        }
    }
}
