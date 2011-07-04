<?php

class Timer
{
	private $begin;
	private $end;

	public function __construct()
	{
		$this->begin = 0;
		$this->end = 0;
	}
	
	public function start()
	{
		$this->begin = microtime(true);
	}
	
	public function stop()
	{
		$this->end = microtime(true);
	}
	
	public function getDuration($round)
	{
		return round($this->end - $this->begin,$round);
	}
}