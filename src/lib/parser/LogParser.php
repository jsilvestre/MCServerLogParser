<?php

class LogParser
{
	
	private $timer;
	
	private $players;
	
	private $entries;
	
	public function __construct()
	{
		$this->timer = new Timer();
						
		$this->players = array();
		
		$this->entries = array();
	}
	
	public function parse($file)
	{
		$this->timer->start();

		$lastUpdate = strtotime("2010-05-23 20:37:29");

		$path = 'data/logs/'.$file;

		$lines = array_reverse(file($path)); // in order to go through a minimum of lines until we met the lastUpdate date
		
		$relevantCriterias = array('connection','tp','give','timing');

		foreach($lines as $element)
		{
			$entry="";

			if(preg_match("#([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}) (\[[A-Z]+\]) (.*)#",$element,$matches))
			{
				if(strtotime($matches[1]) <= $lastUpdate)
				{
					break;
				}

				if(preg_match("#(\w+) \[.*\].*logged.*#",$matches[3],$info))
				{
					$action = "connection";
				}
				else if(preg_match("#(\w+).*lost connection.*#",$matches[3],$info))
				{
					$action = "disconnection";		
				}
				else if(preg_match("#(\w+): Giving .* some ([0-9]+)#",$matches[3],$info))
				{
					$action = "give";
				}
				else if(preg_match("#(\w+): Teleporting .* to .*#",$matches[3],$info))
				{
					$action = "tp";	
				}
				else if(preg_match("#(\w+): Set time to [0-9]+#",$matches[3],$info))
				{
					$action = "timing";	
				}

				if(isset($info[1]))
				{
					if(!preg_match("#CONSOLE|Player|Player[0-9]+|^[0-9]#",$info[1]))
					{	
						$entry = array(
										'date' => $matches[1],
										'nick' => $info[1],
										'action' => $action,
										'raw' => $matches[3]
									);
					}
				}

				if(!empty($entry)) {
					$this->entries[] = $entry;		

					// 
					if(!array_key_exists($entry['nick'],$this->players))
					{
						$this->players[$entry['nick']] = new Player($entry['nick']);
					}

					if(in_array($entry['action'],$relevantCriterias))
						$this->players[$entry['nick']]->increaseStat($entry['action']);
				}
			}
		}

		$this->timer->stop();

		$fin = $this->entries[0]['date'];
		$debut = $this->entries[count($this->entries)-1]['date'];
	}
	
	public function getEntries()
	{
		return $this->entries;
	}
	
	public function getPlayers()
	{
		return $this->players;
	}
	
	public function getTimer()
	{
		return $this->timer;
	}
}