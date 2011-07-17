<?php
$display="";

$playerP = new PlayerParser();
$playerP->parse("players.xml");
$players = $playerP->getPlayers();
$renderTimer = new Timer();
$renderTimer->start();

$display.= 'Last Update : '.$playerP->getLastUpdate().'<br />';
$display.= '==STATS GENERALES==<br />';
$display.='number of connections: '.getTotal($players,'connection').'<br />';
$display.='numver of /gives: '.getTotal($players,'give').'<br />';
$display.='number of /tps: '.getTotal($players,'tp').'<br />';
$display.='number of /set time: '.getTotal($players,'timing').'<br />';
$display.='Total uptime: '.formatDurationArray(durationToArray(getTotal($players,'uptime','total'))).'<br />';

$criterias = array('connection','give','tp','timing','uptime');
foreach($criterias as $criteria)
{
	$currentCriteria = $criteria;
	$display.='<br />==STATS '.$criteria.' ==</br>';
	uasort($players,'cmp');
	foreach($players as $nick => $player)
	{
		if($criteria=='uptime')
			$option="total";
		else
			$option=null;
			
		$total = (getTotal($players,$criteria,$option) == 0) ? 1 : getTotal($players,$criteria,$option);
		$info = "";

		if($criteria != "uptime" && $criteria != "connection")
		{
			$uptime = ($player->getStat("uptime",'total') == 0) ? 1 : $player->getStat("uptime",'total');
			$info = 'soit '.round($player->getStat($criteria) / ($uptime/(60*60)),2).' per hour';
		}
		else if($criteria == "connection")
		{
			$player->getStat("uptime");
			$nbConnection = ($player->getStat('connection') == 0) ? 1 : $player->getStat('connection');
			$averageDuration = round($player->getStat("uptime",'total') / $nbConnection,2);
			$info = 'average duration='.formatDurationArray(durationToArray($averageDuration)).'. Min='.formatDurationArray(durationToArray($player->getStat('uptime','shortest'))).' - Max='.formatDurationArray(durationToArray($player->getStat('uptime','longest')));
		}

		
		$display.= $nick.' : '.$player->getFormattedStat($criteria).' ('.round($player->getStat($criteria,$option)/$total*100,2).'%) '.$info.'<br />';
	}
}

$renderTimer->stop();

$display.= '<br /><br />Generated in '.$renderTimer->getDuration(4).'s.<br />';


?>

<div id="wrapper">
	<div class="box">
		<h2>Overall statistics</h2>
		<?php echo $display; ?>
	</div>
</div>