<?php

class SimpleXMLElementExt extends SimpleXMLElement
{

/**
 * Add SimpleXMLElement code into a SimpleXMLElement
 * @param SimpleXMLElement $append
 */
public function appendXML($append)
{
    if (!empty($append)) {
        if (strlen(trim((string) $append))==0) {
            $xml = $this->addChild($append->getName());
            foreach($append->children() as $child) {
                $xml->appendXML($child);
            }
        } else {
            $xml = $this->addChild($append->getName(), (string) $append);
        }
        foreach($append->attributes() as $n => $v) {
			if(!array_key_exists($n,$xml->attributes()))
            	$xml->addAttribute($n, $v);
        }
    }
}

}