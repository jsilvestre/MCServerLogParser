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
			$this->stats[$criteria][$option] = $this->stats[$criteria][$option] + $value;
		}
		else
		{
			$this->stats[$criteria] = $this->stats[$criteria] + $value;
		}
	}
}