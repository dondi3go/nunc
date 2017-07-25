<?php

require_once(__DIR__.'/../core/FileSystem.php');
require_once(__DIR__.'/../nubs/RSSChannels.php');
require_once(__DIR__.'/../nubs/RSSItems.php');



class RSSReader
{
    // RSS Versions
    const UNKNOWN = 0;
    const RSS1    = 1;
    const RSS2    = 2;
    const ATOM    = 3;

    //
    // Read several RSSChannel, fill in several RSSItems
    //
    public function readChannels($channels, $items)
    {
        $imax = $channels->getObjectCount();
        for($i=0; $i<$imax; $i++)
        {
            $channel = $channels->getObjectByIndex($i);
            $this->readChannel($channel, $items);
        }
    }

    //
    // Read one RSSChannel, fill in several RSSItems
    //
    public function readChannel($channel, $items)
    {
        $url = $channel->getPropertyValue(RSSChannel::SrcUrl);
        $nbMaxItems = 5;
        try
        {
            $file_raw = $this->loadUrl($url);
            if($file_raw !== false)
            {
                $parsed_xml = simplexml_load_string($file_raw);
                if($parsed_xml !== false)
                {
                    $version = $this->getRSSVersion($parsed_xml);
                    switch($version)
                    {
                        case RSS1:
                            $this->parseRSS1($parsed_xml, $nbMaxItems, $items);
                            return true;
                            break;
                        case RSS2:
                            $this->parseRSS2($parsed_xml, $nbMaxItems, $items);
                            return true;
                            break;
                        case ATOM:
                            $this->parseATOM($parsed_xml, $nbMaxItems, $items);
                            return true;
                            break;
                        default:
                            return false;
                    }
                }
            }
        }
        catch(Exception $e)
        {}
        return false;
    }

    //
    //
    //
    public function checkChannels($channels, $items)
    {
        $imax = $channels->getObjectCount();
        for($i=0; $i<$imax; $i++)
        {
            $channel = $channels->getObjectByIndex($i);
            $this->checkChannel($channel, $items);
        }
    }

    //
    //
    //
    public function checkChannel($channel, $items)
    {
        $url = $channel->getPropertyValue(RSSChannel::SrcUrl);
        $channelName = $channel->getPropertyValue(RSSChannel::Name);
        try
        {
            $startTime = microtime(true);
            $file_raw = $this->loadUrl($url);
            $stopTime = microtime(true);
            $loadTime = $stopTime - $startTime;

            $text = $url."<br/>load time = ".$loadTime;

            if($file_raw === false)
            {
                $newItem = $items->addObject();
                $newItem->setPropertyValue(RSSItem::Title, $channelName." : LOAD ERROR");
                $newItem->setPropertyValue(RSSItem::Text, $text);
                return false;
            }
            else
            {
                $parsed_xml = simplexml_load_string($file_raw);
                if($parsed_xml === false)
                {
                    $newItem = $items->addObject();
                    $newItem->setPropertyValue(RSSItem::Title, $channelName." : PARSE ERROR");
                    $newItem->setPropertyValue(RSSItem::Text, $text."<br/><pre>".$file_raw."</pre>");
                    return false;
                }

                $version = $this->getRSSVersion($parsed_xml);
                
                $newItem = $items->addObject();
                $newItem->setPropertyValue(RSSItem::Title, $channelName." : ".$version);
                $newItem->setPropertyValue(RSSItem::Text, $text);
                return true;
            }
        }
        catch (Exception $e)
        {
            $newItem = $items->addObject();
            $newItem->setPropertyValue(RSSItem::Title, $channelName." : ERROR");
            $text = $url."<br/>".$e->getMessage();
            $newItem->setPropertyValue(RSSItem::Text, $text);
            return false;
        }
    }

    //
    //
    //
    private function getRSSVersion($parsed_xml)
    {
        $name = $parsed_xml->getName();

        // is it RSS1.0, RSS 2.0 or ATOM ?
        $version = 0;
        
        if( $name == "RDF" ){
            $version = RSS1;
        }
        else if( $name == "rss" ){
            $version = RSS2;
        }
        else if( $name == "feed" ){
            $version = ATOM;
        }

        return $version;
    }

    //
    // TODO : handle "https://"
    //
    private function loadUrl($url)
    {
        if(!strstr($url, 'https'))
        {
            return file_get_contents($url);
        }
        else
        {
            //return file_get_contents($url, false, stream_context_create(array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false))));
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            //curl_setopt( $ch, CURLOPT_AUTOREFERER, TRUE );

            //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
            //curl_setopt($ch, CURL_SSLVERSION_TLSv1_1);

            //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT');
            //curl_setopt($ch, CURL_SSLVERSION_TLSv1_1);

            curl_setopt($c, CURLOPT_SSLVERSION, 0);
            //curl_setopt($ch, CURLOPT_SSLVERSION, 1); // changing this make some https fail

            $result = curl_exec($ch);
            if ($result === false) {
                $result = curl_error($ch);
            }
            curl_close($ch);

            return $result;
        }
    }

    private function filterText($text)
    {
        return strip_tags($text);
    }

    private function parseRSS1($parsed_xml, $nbMaxItems, $items)
    {
        $nbItems = 0;
        foreach($parsed_xml->item as $item)
        {
            $newItem = $items->addObject();
            $newItem->setPropertyValue(RSSItem::Title, ucfirst($item->title));
            $newItem->setPropertyValue(RSSItem::SrcUrl, $item->link);
            $newItem->setPropertyValue(RSSItem::Text, $this->filterText($item->description));
            
            $nbItems ++;
            if($nbItems >= $nbMaxItems) {
                return;
            }
        }
    }

    private function parseRSS2($parsed_xml, $nbMaxItems, $items)
    {
        $nbItems = 0;
        foreach($parsed_xml->channel->item as $item)
        {
            $newItem = $items->addObject();
            $newItem->setPropertyValue(RSSItem::Title, ucfirst($item->title));
            $newItem->setPropertyValue(RSSItem::SrcUrl, $item->link);
            $newItem->setPropertyValue(RSSItem::Text, $this->filterText($item->description));
            
            $nbItems ++;
            if($nbItems >= $nbMaxItems) {
                return;
            }
        }
    }

    private function parseATOM($parsed_xml, $nbMaxItems, $items)
    {
        $newItem = $items->addObject();
        $newItem->setPropertyValue(RSSItem::Title, "TODO : ATOM");
    }


}

?>