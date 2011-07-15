<?php


if(!empty($_POST['updateLog']))
{
	$logP = new LogParser();
	$entryP = new EntryParser();
	$playerP = new PlayerParser();

	$playerP->parse("players.xml");
	$logP->setLastUpdate($playerP->getLastUpdate()); // we set the limit so the log parser won't go through the whole file
	$logP->parse("server.last.log");

	$entryP->addEntries($logP->getEntries());
	$entryP->save("entries.xml");

	$xmlPlayers = $playerP->getPlayers();

	foreach($logP->getPlayers() as $logPlayer)
	{

		$logPlayer->computeUptime($entryP->getEntries());
	
		if(array_key_exists($logPlayer->getNick(),$xmlPlayers))
		{ // we must add the new values to the old ones
			$xmlPlayers[$logPlayer->getNick()]->increaseStat('connection',null,$logPlayer->getStat('connection'));
			$xmlPlayers[$logPlayer->getNick()]->increaseStat('give',null,$logPlayer->getStat('give'));
			$xmlPlayers[$logPlayer->getNick()]->increaseStat('tp',null,$logPlayer->getStat('tp'));
			$xmlPlayers[$logPlayer->getNick()]->increaseStat('timing',null,$logPlayer->getStat('timing'));
			$xmlPlayers[$logPlayer->getNick()]->increaseStat('uptime','total',$logPlayer->getStat('uptime','total'));
			if($logPlayer[$logPlayer->getNick()]->getStat('uptime','shortest') < $xmlPlayer[$logPlayer->getNick()]->getStat('uptime','shortest'))
				$xmlPlayers[$logPlayer->getNick()]->increaseStat('uptime','shortest',$logPlayer->getStat('uptime','shortest'));
		
			if($logPlayer[$logPlayer->getNick()]->getStat('uptime','longest') > $xmlPlayer[$logPlayer->getNick()]->getStat('uptime','longest'))
				$xmlPlayers[$logPlayer->getNick()]->increaseStat('uptime','longest',$logPlayer->getStat('uptime','longest'));
		}
		else
		{ // just add the new player
			$playerP->addPlayer($logPlayer);
		}
	}

	$playerP->setLastUpdate($logP->getLastUpdate());
	$playerP->save("players.xml");
	
	
	$display = '<p>The statistics have been correctly updated.</p>';
	$display.= '<p>'.count($logP->getEntries()).' entries have been treated in '.$logP->getTimer()->getDuration(4).'s</p>';
}
else
{
	$display='
		<p><form method="post" action="index.php?page=admin">
				<input type="file" name="logFile" />
				<input type="submit" name="updateLog" value="Update !" />
		</form></p>
	';
}

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