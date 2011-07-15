<?php

class Player
{
	private $nick;
	private $stats;
	private $isOnline;
	
	public function __construct($nick)
	{
		$this->nick = $nick;
		
		$this->stats = array(
				'connection'	=> 0,
				'give'			=> 0,
				'tp'			=> 0,
				'timing'		=> 0,
				'uptime'		=> array(
										'total'		=> 0,
										'shortest'	=> 0,
										'longest'	=> 0
									)			
			);
			
		$this->isOnline = false;
	}
	
	public function getStat($criteria,$option=null)
	{
		if(!array_key_exists($criteria,$this->stats) || (!is_null($option) && !array_key_exists($option,$this->stats[$criteria])))
			throw new Exception("Cannot retrieve criteria $criteria with option $option");
			
		if(!is_null($option))
		{
			return $this->stats[$criteria][$option];
		}
		else
		{
			return $this->stats[$criteria];
		}
	}
	
	public function increaseStat($criteria,$option=null,$value=1)
	{
		if(!array_key_exists($criteria,$this->stats) || (!is_null($option) && !array_key_exists($option,$this->stats[$criteria])))
			throw new Exception("Cannot increase criteria $criteria with option $option and value=$value");		
			
		if(!is_null($option))
		{
			if($option == "total")
				$this->stats[$criteria][$option] = $this->stats[$criteria][$option] + $value;
			else
				$this->stats[$criteria][$option] = $value;
		}
		else
		{
			$this->stats[$criteria] = $this->stats[$criteria] + $value;
		}
	}
	
	public function getNick()
	{
		return $this->nick;
	}
	
	public function computeUptime($entries)
	{				
		$tempConnectionDate = 0;
		$tempDisconnectionDate = 0;
		
		foreach($entries as $entry)
		{	
			
			if($entry['nick'] == $this->getNick())
			{
			
			if($entry['action'] == 'connection')
			{
				if(!$this->isOnline)
				{
					$this->isOnline = true;
					$tempConnectionDate = strtotime($entry['date']);
				}
			}
			else if($entry['action'] == 'disconnection')
			{
				if($this->isOnline)
				{
					$this->isOnline = false;
					$tempDisconnectionDate = strtotime($entry['date']);
					$duration = $tempDisconnectionDate - $tempConnectionDate;
					$this->increaseStat('uptime','total',$duration);
					
					if($duration > $this->getStat('uptime','longest'))
					{
						$this->increaseStat('uptime','longest',$duration);
					}
						
					if($duration < $this->getStat('uptime','shortest') || $this->getStat('uptime','shortest') == 0)
					{
						$this->increaseStat('uptime','shortest',$duration);
					}
				}
			}}
		}
	}
	
	public function toXMLString()
	{
		return '<player nick="'.$this->nick.'">
				<stats>
					<stat id="connection" value="'.$this->getStat('connection').'" />
					<stat id="give" value="'.$this->getStat('give').'" />
					<stat id="tp" value="'.$this->getStat('tp').'" />
					<stat id="timing" value="'.$this->getStat('timing').'" />
					<stat id="uptime">
						<option id="total" value="'.$this->getStat('uptime','total').'" />
						<option id="shortest" value="'.$this->getStat('uptime','shortest').'" />
						<option id="longest" value="'.$this->getStat('uptime','longest').'" />
					</stat>
				</stats>
			</player>';
	}
}