<?php

class EntryParser
{
	private $document;
	
	private $entries;
	
	private $timer;
	
	public function __construct()
	{
		$this->entries = array();
		
		$this->timer = new Timer();
		
		$xmlOrigin = '<entries>'.PHP_EOL.'</entries>';
		
		$this->document = new SimpleXMLElementExt($xmlOrigin);
	}
	
	public function parse($file,array $restrict=array())
	{
		
		$this->timer->start();
		
		$path = "data/".$file;
			
		if(file_exists($path))
		{	
			$this->document = simplexml_load_file($path,'SimpleXMLElementExt');
				
			foreach($this->document->children() as $element)
			{
				$entry = array();
				
				if(count($restrict) == 0)
				{
					// retrieve all the elements
					$entry = array(
									'date' => (string) $element['date'],
									'nick' => (string) $element['nick'],
									'action' => (string) $element['action'],
									'raw' => (string) $element['raw']
								);
				}
				else
				{
					if(count($restrict) == 2 && isset($element[$restrict[0]]) && $element[$restrict[0]] == $restrict[1])
					{
						$entry = array(
										'date' => (string) $element['date'],
										'nick' => (string) $element['nick'],
										'action' => (string) $element['action'],
										'raw' => (string) $element['raw']
									);
					}
			
				}
				
				if(count($entry) > 0)
				{
					$this->entries[] = $entry;
				}
			}
		}
		$this->timer->stop();
	}
	
	public function addEntries(array $entries)
	{
		foreach($entries as $entry)
		{
			$this->addEntry($entry);
		}
	}
	
	public function addEntry(array $entry)
	{
		$xmlEntryStr = '<entry nick="'.$entry['nick'].'" date="'.$entry['date'].'" action="'.$entry['action'].'" raw="'.$entry['raw'].'" />';
		
		$xmlEntry = simpleXML_load_string($xmlEntryStr,'SimpleXMLElementExt');
		$this->document->appendXML($xmlEntry);		
					
		$this->entries[] = $entry;		
	}
	
	public function save($file)
	{
		$path = "data/".$file;
		
		// We use DOM to format the output
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($this->document->asXML());
		$dom->save($path);
	}
		
	public function getEntries()
	{
		return $this->entries;
	}
	
	public function getTimer()
	{
		return $this->timer;
	}
}