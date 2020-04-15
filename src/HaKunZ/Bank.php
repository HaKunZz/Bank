<?php

/*
*	F U R Q O N B U K H O R I / H A K U N Zz
*	DISCORD: HaKunZ#4133
*/

namespace HaKunZ;

use pocketmine\Server;
use pocketmine\Player;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;
use pocketmine\item\Item;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

use pocketmine\utils\Config;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\ModalForm;
use jojoe77777\FormAPI\CustomForm;

use onebone\economyapi\EconomyAPI;

class Bank extends PluginBase implements Listener {
	
	public function onEnable(){
		$this->getLogger()->info(C::GREEN . "Bank By HaKunZz Online");
		$this->economy = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		@mkdir($this->getDataFolder());
		$this->bank = new Config($this->getDataFolder() . "bank.yml", Config::YAML, array());
		$this->bank->save();
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "bank":
			    if($sender instanceof Player){
					$this->Menu($sender);
					return true;
				}else{
					$sender->sendMessage("§cPlease Use This Command In Game!");
					return true;
				}
		}
	}
	
	public function Menu($sender){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $sender, int $data = null) {
			$result = $data;
			if($result === null){
				return true;
			}
	       switch($result){
		        case 0:
		        $sender->addTitle("§cGOODBYE", "BANK UI");
		        break;
	            case 1:
	            $this->SaveMoney($sender);
	            break;
	            case 2:
	            $this->TakeMoney($sender);
	            break;
                }
            });
            $mb = $this->getMoneyInBank($sender);
            $form->setTitle("§8-=§6MenuBank§8=-");
            $form->setContent("§6Hii §8" . $sender->getName() . "\n§6Money: §8" . $this->economy->myMoney($sender) . "\n§6Money In Bank: §8" . $mb . "\n\n§6> §8Select Menu In Below:");
            $form->addButton("§l§cEXIT\n§r§8EXIT FROM MENU",0,"textures/ui/cancel");
            $form->addButton("§l§aSAVE MONEY\n§r§8SAVE YOUR MONEY IN BANK",0,"textures/ui/luck_effect");
            $form->addButton("§l§aTAKE MONEY\n§r§8TAKE MONEY IN BANK",0,"textures/ui/luck_effect");
            $form->sendToPlayer($sender);
            return $form;
	}
	
	public function SaveMoney($sender){
		$formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createCustomForm(function(Player $sender, $data){
			$result = $data[0];
			if($result === null){
				return true;
			}
			if(trim($data[0]) === "") {
				$sender->sendMessage("§cPlease Input!");
				return true;
			}
			if(is_numeric($data[0])){
				$money = $this->economy->myMoney($sender);
				if($money >= $data[0]){
					EconomyAPI::getInstance()->reduceMoney($sender, $data[0]);
					$sender->sendMessage("§aYou Succesfuly Save Money In Bank As Much §6" . $data[0]);
					$this->addMoney($sender, $data[0]);
				}else{
					$sender->sendMessage("§cYou Don't Have Enough Money As Much §6" . $data[0] . " §cTo Save In Bank!");
				}
			}else{
				$sender->sendMessage("§cPlease Input A Number!");
				return true;
			}
		});
		$form->setTitle("SAVE MONEY");
		$form->addInput("Input Amount Money:");
        $form->sendToPlayer($sender);
        return $form;
	}
	
	public function TakeMoney($sender){
		$formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createCustomForm(function(Player $sender, $data){
			$result = $data[0];
			if($result === null){
				return true;
			}
			if(trim($data[0]) === "") {
				$sender->sendMessage("§cPlease Input!");
				return true;
			}
			if(is_numeric($data[0])){
				$money = $this->economy->myMoney($sender);
				$mb = $this->getMoneyInBank($sender);
				if($mb >= $data[0]){
					EconomyAPI::getInstance()->addMoney($sender, $data[0]);
					$sender->sendMessage("§aYou Succesfuly Take Money In Bank As Much §6" . $data[0]);
					$this->reduceMoney($sender, $data[0]);
				}else{
					$sender->sendMessage("§cYou Don't Have Enough Money In Bank As Much §6" . $data[0] . " §cTo Take Money!");
				}
			}else{
				$sender->sendMessage("§cPlease Input A Number!");
				return true;
			}
		});
		$form->setTitle("TAKE MONEY");
		$form->addInput("Input Amount Money:");
        $form->sendToPlayer($sender);
        return $form;
	}
	
	public function addMoney($sender, $int){
		$hakunz = strtolower($sender->getName());
		$this->bank->set($hakunz, $this->bank->get($hakunz) + $int);
		$this->bank->save();
	}
	
	public function reduceMoney($sender, $int){
		$hakunz = strtolower($sender->getName());
		$this->bank->set($hakunz, $this->bank->get($hakunz) - $int);
		$this->bank->save();
	}
	
	public function getMoneyInBank($sender){
		$hakunz = strtolower($sender->getName());
		return $this->bank->get($hakunz);
	}
}