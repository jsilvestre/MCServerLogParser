<?php

function getTotal($array,$criteria,$option=null)
{
	$total = 0;
	
	foreach($array as $element)
	{
		$total = $total + $element->getStat($criteria,$option);
	}
	
	return $total;
}

function durationToArray($duration)
{
	$duration = abs($duration);
	$converted = array();

	$converted['days'] = floor($duration / (24*3600));
	$converted['hours'] = floor(($duration / 3600) % 24);	
	$converted['minutes'] = floor(($duration / 60) % 60);
	$converted['seconds'] = floor($duration % 60);

	return $converted;
}

function formatDurationArray(array $duration)
{
	return $duration['days'].'d '.$duration['hours'].'h '.$duration['minutes'].'m '.$duration['seconds'].'s';
}