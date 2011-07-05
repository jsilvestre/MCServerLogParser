<?php

class PlayerParser
{
	private $timer;
	
	private $players;
	
	private $lastUpdateOn;
	
	private $document;
	
	public function __construct()
	{
		$this->timer = new Timer();
		
		$this->players = array();
		
		$this->lastUpdateOn = "1970-01-01 00:00:00";
		
		$xmlOrigin = '
			<players lastUpdateOn="'.$this->lastUpdateOn.'">'.PHP_EOL.'</players>		
		';
		$this->document = new SimpleXMLElementExt($xmlOrigin);
	}
	
	public function parse($file)
	{
		$this->timer->start();
		
		$path = "data/".$file;
		
		if(file_exists($path))
		{
			$this->document = simplexml_load_file($path,'SimpleXMLElementExt');
	
			$this->lastUpdateOn = (string) $this->document['lastUpdateOn'];
			
			foreach($this->document->children() as $element)
			{
				$player = new Player((string)$element['nick']);
		
				foreach($element->stats->children() as $stat)
				{
					if((string)$stat['id'] == "uptime")
					{
						foreach($stat->children() as $option)
						{
							$player->increaseStat((string)$stat['id'],(string)$option['id'],(int)$option['value']);	
						}
					}
					else
					{
						$player->increaseStat((string)$stat['id'],null,(int)$stat['value']);				
					}
				}

				$this->players[] = $player;
			}
		}
		
		$this->timer->stop();						
	}
	
	public function addPlayers(array $players)
	{
		foreach($players as $player)
		{
			$this->addPlayer($player);
		}
	}
	
	public function addPlayer(Player $player)
	{
		$xmlPlayer = simpleXML_load_string($player->toXMLString(),'SimpleXMLElementExt');
		$this->document->appendXML($xmlPlayer);		
					
		$this->players[] = $player;
	}
	
	public function save($file)
	{
		$path = "data/".$file;
		
		// We use DOM to format the output
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($this->getDocument()->asXML());
		$dom->save($path);
	}
	
	public function getPlayers()
	{
		return $this->players;
	}
	
	public function getTimer()
	{
		return $this->timer;
	}
	
	public function getDocument()
	{
		return $this->document;
	}
	
	public function getLastUpdate()
	{
		return $this->lastUpdateOn;
	}
	
	public function setLastUpdate($lastUpdate)
	{
		$this->lastUpdateOn = $lastUpdate;
		$this->document['lastUpdateOn'] = $lastUpdate;
	}
}