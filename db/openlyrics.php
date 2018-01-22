<?php
/**
 * Nextcloud - Notes
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Bernhard Posselt <dev@bernhard-posselt.com>
 * @copyright Bernhard Posselt 2012, 2014
 */

namespace OCA\OpenLP\Db;

use OCP\AppFramework\Db\Entity;

/**
 * Class OpenLyrics
 * @package OCA\OpenLP\Db
 */
class OpenLyrics extends Entity {


    

    /**
     * @param String $xml
     * @return static
     */
    public static function getPropertiesFromXML(String $xml){
        $song = new static();
        $song->setId($file->getId());
        $song->setContent(self::convertEncoding($file->getContent()));
        $song->setModified($file->getMTime());
        $song->setTitle(pathinfo($file->getName(),PATHINFO_FILENAME)); // remove extension
        $subdir = substr(dirname($file->getPath()), strlen($songsFolder->getPath())+1);
        $song->setCategory($subdir ? $subdir : null);
        if(is_array($tags) && in_array(\OC\Tags::TAG_FAVORITE, $tags)) {
            $song->setFavorite(true);
            //unset($tags[array_search(\OC\Tags::TAG_FAVORITE, $tags)]);
        }
        $song->updateETag();
        $song->resetUpdatedFields();
        return $song;
    }

    public function __construct($xml_content)
    {
        $this->song_xml = simplexml_load_string($xml_content);

        
        $this->process_titles();
        $this->process_lyrics();
        $this->process_metadata();
        $this->process_authors();
        $this->process_ccli_number();
        $this->process_copyright();
    }

    private static function convertEncoding($str) {
        if(!mb_check_encoding($str, 'UTF-8')) {
            $str = mb_convert_encoding($str, 'UTF-8');
        }
        return $str;
    
    }

    public function process_metadata()
    {
        
        if(isset($this->song_xml['version']))
        {
            $this->metadata->version = $this->song_xml['version']->__toString();
        }
        else
        {
            $this->metadata->version = '';
        }

        if(isset($this->song_xml['createdIn']))
        {
            $this->metadata->createdIn = $this->song_xml['createdIn']->__toString();
        }
        else
        {
            $this->metadata->createdIn = '';
        }

        if(isset($this->song_xml['modifiedIn']))
        {
            $this->metadata->modifiedIn = $this->song_xml['modifiedIn']->__toString();
        }
        else
        {
            $this->metadata->modifiedIn = '';
        }

        if(isset($this->song_xml['modifiedDate']))
        {
            $this->metadata->modifiedDate = $this->song_xml['modifiedDate']->__toString();
        }
        else
        {
            $this->metadata->modifiedDate = '';
        }
        
    }

    public function process_authors()
    {
        foreach($this->song_xml->properties->authors->author as $author) { 
            $authorObject->value = $author->__toString();
            if(isset($author['type']))
            {
                $authorObject->type = $author['type']->__toString();
                
                if(strcmp($authorObject->type,"translation") === 0 )
                {
                    if(isset($author['lang']))
                    {
                        $authorObject->lang = $author['lang']->__toString();
                    }
                    else{
                        $authorObject->lang = "en";
                    }
                }
            }
            $this->properties->authors[] = $authorObject;
            unset($authorObject);            
        }
    }
  

    private function process_copyright()
    {
        if(isset($song_xml['copyright']))
        {
            $this->properties->copyright = $song_xml['copyright']->__toString();
        }
        else
        {
            $this->properties->copyright = '';
        }
    }
    
    private function process_ccli_number()
    {
        if(isset($song_xml['ccliNo']))
        {
            $this->properties->ccli_number = $song_xml['ccliNo']->__toString();
        }
        else
        {
            $this->properties->ccli_number = '';
        }
    }

    private function process_titles() {
        
        foreach($this->song_xml->properties->titles->title as $title) { 
            $titleObject->value = $title->__toString();

            if(isset($title['lang']))
            {
                $titleObject->lang = $title['lang']->__toString();
            }

            if(isset($title['original']))
            {
                $titleObject->original = $title['original']->__toString();
            }
            else
            {
                $titleObject->original = "false";
            }

            $this->properties->titles[] = $titleObject;
            unset($titleObject);            
        }
        
        
    } 

    private function process_lyrics()
    {
        

        if(isset($this->song_xml->properties->verseOrder))
        {
            $this->verse_order = $this->song_xml->properties->verseOrder->__toString();
        }

        foreach($this->song_xml->lyrics->verse as $verse) { 

            $verseObject;
            
            $text = '';

            foreach($verse->lines as $lines)
            {
                
                $line = $lines->asxml();//$lines->__toString();
                 $line = str_replace("<br/>","\r\n",$line); 
                 $line = str_replace(['<lines>',"</lines>"],'',$line);
                 $verseObject->lines[] = $line;
                 //$verseObject->lines[] = $this->process_lines_mixed_content($lines);

            }

            if(isset($verse['name']))
            {
                $verseObject->name = $verse['name']->__toString();
            }
            else
            {
                $verseObject->name = '';
            }
            if(isset($verse['lang']))
            {
                $verseObject->lang = $verse['lang']->__toString();
            }
            else
            {
                $verseObject->lang = '';
            }

            $this->verses[] = $verseObject;
            unset($verseObject); 
        }

    }

    private function process_lines_mixed_content($lines)
    {
        return "";
    }
    
       

    private $song_xml;
    public $verses;
    public $metadata;
    public $properties;
    
}
