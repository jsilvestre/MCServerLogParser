<?php
$display="";

$playerP = new PlayerParser();
$playerP->parse("players.xml");
$players = $playerP->getPlayers();
$renderTimer = new Timer();
$renderTimer->start();

$display.= 'Last Update : '.$playerP->getLastUpdate().'<br />';
$display.= '==STATS GENERALES==<br />';
$display.='Nombre de connexion : '.getTotal($players,'connection').'<br />';
$display.='Nombre de give : '.getTotal($players,'give').'<br />';
$display.='Nombre de tp : '.getTotal($players,'tp').'<br />';
$display.='Nombre de set time : '.getTotal($players,'timing').'<br />';
$display.='Uptime cumulée : '.formatDurationArray(durationToArray(getTotal($players,'uptime','total'))).'<br />';

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
			$info = 'avec une durée de connexion moyenne de '.formatDurationArray(durationToArray($averageDuration)).'. Min='.formatDurationArray(durationToArray($player->getStat('uptime','shortest'))).' - Max='.formatDurationArray(durationToArray($player->getStat('uptime','longest')));
		}

		
		$display.= $nick.' : '.$player->getFormattedStat($criteria).' ('.round($player->getStat($criteria,$option)/$total*100,2).'%) '.$info.'<br />';
	}
}

$renderTimer->stop();

$display.= '<br /><br />Rendu généré en '.$renderTimer->getDuration(4).' secondes.<br />';


?>

<div id="wrapper">
	<div class="box">
		<h2>Accueil</h2>
		<?php echo $display; ?>
	</div>
</div>

<div id="action">
	<h2>Actions possibles</h2>
	<p>Aucune action possible...</p>
</div>