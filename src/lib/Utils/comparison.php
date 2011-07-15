<?php

global $currentCriteria; // ugly but allow to use a single function instead of one per criteria...

function cmp($a,$b)
{
	global $currentCriteria;

	if($currentCriteria == "uptime")
	{
		$option = "total";
	}
	else
	{
		$option = null;
	}
	
	if($a->getStat($currentCriteria,$option) == $b->getStat($currentCriteria,$option))
	{
		return 0;
	}
	
	return ($a->getStat($currentCriteria,$option) > $b->getStat($currentCriteria,$option)) ? -1 : 1;	
}