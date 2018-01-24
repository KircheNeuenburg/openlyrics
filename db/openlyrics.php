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

use DOMDocument;
use DateTime;
use OCP\AppFramework\Db\Entity;

/**
 * Class OpenLyrics
 * @package OCA\OpenLP\Db
 */
class OpenLyrics extends Entity {


    

    

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

    public function export_xml()
    {
        $dom = new DOMDocument('1.0', 'utf-8');

        $song = $dom->createElement('song');
        
        $attr_xmlns = $dom->createAttribute('xmlns');
        $attr_xmlns->value = 'http://openlyrics.info/namespace/2009/song';
        $song->appendChild($attr_xmlns);

        $attr_version = $dom->createAttribute('version');
        $attr_version->value = '0.8';
        $song->appendChild($attr_version);

        $attr_created_in = $dom->createAttribute('createdIn');
        $attr_created_in->value = $this->metadata->created_in;
        $song->appendChild($attr_created_in);

        $attr_modified_in = $dom->createAttribute('modifiedIn');
        $attr_modified_in->value = 'OpenLyrics 0.1.0';
        $song->appendChild($attr_modified_in);

        $attr_modified_date = $dom->createAttribute('modifiedDate');
        $attr_modified_date->value = date(DateTime::ISO8601);
        $song->appendChild($attr_modified_date);

        $properties = $dom->createElement('properties');

        $titles = $dom->createElement('titles');
        foreach($this->properties->titles as $title)
        {
            $title_el = $dom->createElement('title',$title->value);

            if($title->original == 'true')
            {
                $attr_original = $dom->createAttribute('original');
                $attr_original->value = $title->original;
                $title_el->appendChild($attr_original);
            }
            if($title->lang != '')
            {
                $attr_lang = $dom->createAttribute('lang');
                $attr_lang->value = $title->lang;
                $title_el->appendChild($attr_lang);
            }

            $titles->appendChild($title_el);

        }
        $properties->appendChild($titles);

        $authors = $dom->createElement('authors');
        foreach($this->properties->authors as $author)
        {
            $author_el = $dom->createElement('author',$author->value);
            if($author->type != '')
            {
                $attr_type = $dom->createAttribute('type');
                $attr_type->value = $author->type;
                $author_el->appendChild($attr_type);
            }
            if($author->lang)
            {
                $attr_lang = $dom->createAttribute('lang');
                $attr_lang->value = $author->lang;
                $author_el->appendChild($attr_lang);
            }
            $authors->appendChild($author_el);

        }
        $properties->appendChild($authors);

        $song->appendChild($properties);
        $dom->appendChild($song);

        return $dom->saveXML();
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
            $this->metadata->created_in = $this->song_xml['createdIn']->__toString();
        }
        else
        {
            $this->metadata->created_in = '';
        }

        if(isset($this->song_xml['modifiedIn']))
        {
            $this->metadata->modified_in = $this->song_xml['modifiedIn']->__toString();
        }
        else
        {
            $this->metadata->modified_in = '';
        }

        if(isset($this->song_xml['modifiedDate']))
        {
            $this->metadata->modified_date = $this->song_xml['modifiedDate']->__toString();
        }
        else
        {
            $this->metadata->modified_date = '';
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
