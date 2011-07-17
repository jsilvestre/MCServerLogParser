<?php

$display="";

if(!empty($_POST['updateLog']))
{
	if(isset($_FILES['logFile']) AND $_FILES['logFile']['error'] == 0)
	{
	
        $infosfichier = pathinfo($_FILES['logFile']['name']);
        $extension_upload = $infosfichier['extension'];
        $extensions_autorisees = array('log');
        if (in_array($extension_upload, $extensions_autorisees))
        {
			$time = time();
			$filename = strftime("%F",$time).'-'.strftime("%T",$time).'-'.basename($_FILES['logFile']['name']);
			move_uploaded_file($_FILES['logFile']['tmp_name'], 'data/logs/'.$filename);
	
			$logP = new LogParser();
			$entryP = new EntryParser();
			$playerP = new PlayerParser();

			$playerP->parse("players.xml");
			$logP->setLastUpdate($playerP->getLastUpdate()); // we set the limit so the log parser won't go through the whole file
			if(file_exists('data/logs/'.$filename))
			{
				$logP->parse($filename);

				$entryP->addEntries($logP->getEntries());
				$entryP->save("entries.xml");

				$xmlPlayers = $playerP->getPlayers();
				
				$logPlayers = $logP->getPlayers();

				foreach($logPlayers as $logPlayer)
				{
					$logPlayer->computeUptime($entryP->getEntries());
	
					if(array_key_exists($logPlayer->getNick(),$xmlPlayers))
					{ // we must add the new values to the old ones
						$xmlPlayers[$logPlayer->getNick()]->increaseStat('connection',null,$logPlayer->getStat('connection'));
						$xmlPlayers[$logPlayer->getNick()]->increaseStat('give',null,$logPlayer->getStat('give'));
						$xmlPlayers[$logPlayer->getNick()]->increaseStat('tp',null,$logPlayer->getStat('tp'));
						$xmlPlayers[$logPlayer->getNick()]->increaseStat('timing',null,$logPlayer->getStat('timing'));
						$xmlPlayers[$logPlayer->getNick()]->increaseStat('uptime','total',$logPlayer->getStat('uptime','total'));
						if($logPlayers[$logPlayer->getNick()]->getStat('uptime','shortest') < $xmlPlayers[$logPlayer->getNick()]->getStat('uptime','shortest'))
							$xmlPlayers[$logPlayer->getNick()]->increaseStat('uptime','shortest',$logPlayer->getStat('uptime','shortest'));
		
						if($logPlayers[$logPlayer->getNick()]->getStat('uptime','longest') > $xmlPlayers[$logPlayer->getNick()]->getStat('uptime','longest'))
							$xmlPlayers[$logPlayer->getNick()]->increaseStat('uptime','longest',$logPlayer->getStat('uptime','longest'));
					}
					else
					{ // just add the new player
						$playerP->addPlayer($logPlayer);
					}
				}

				$playerP->setLastUpdate($logP->getLastUpdate());
				$playerP->save("players.xml");
		
				$display.= '<p>The statistics have been correctly updated.</p>';
				$display.= '<p>'.count($logP->getEntries()).' entries have been treated in '.$logP->getTimer()->getDuration(4).'s</p>';
			}
			else
			{
				$display.="<p>the uploaded file cannot be found.</p>";
			}
	    }
	}
	else
	{
		$display.="<p>An error occured during the upload</p>";
	}
}
else
{
	$display='
		<p><form method="post" action="index.php?page=admin" enctype="multipart/form-data">
				<input type="file" name="logFile" />
				<input type="submit" name="updateLog" value="Update !" />
		</form></p>
	';
}

?>
<div id="wrapper">
	<div class="box">
		<h2>Upload</h2>
		<?php echo $display; ?>
	</div>
</div>