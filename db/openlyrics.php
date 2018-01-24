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

        $song_dom = new DOMDocument('1.0', 'utf-8');
        $song_dom->loadXML($xml_content);
        $this->song_dom = $song_dom->getElementsByTagName('song')->item(0);

        $this->process_titles();
        $this->process_lyrics();
        $this->process_metadata();
        $this->process_authors();
        $this->process_ccli_number();
        $this->process_copyright();
        $this->process_verse_order();
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
            if($author->lang != '')
            {
                $attr_lang = $dom->createAttribute('lang');
                $attr_lang->value = $author->lang;
                $author_el->appendChild($attr_lang);
            }
            $authors->appendChild($author_el);

        }
        $properties->appendChild($authors);

        if($this->properties->copyright!= '')
        {
            $properties->appendChild($this->export_copyright($dom));
        }
        if($this->properties->ccli_number != '')
        {
            $properties->appendChild($this->export_ccli_number($dom));
        }
        if($this->properties->verse_order != '')
        {
            $properties->appendChild($this->export_verse_order($dom));
        }
        $song->appendChild($properties);
        $dom->appendChild($song);

        return $dom->saveXML();
    }

    private function export_copyright(DOMDocument $dom)
    {
        return $dom->createElement('copyright',$this->properties->copyright);
    }

    private function export_ccli_number(DOMDocument $dom)
    {
        return $dom->createElement('ccliNo',$this->properties->ccli_number);
    }

    private function export_verse_order(DOMDocument $dom)
    {
        return $dom->createElement('verseOrder',$this->properties->verse_order);
    }
    
    private function process_metadata()
    {
        $this->metadata->version = $this->song_dom->getAttribute('version');
        $this->metadata->created_in = $this->song_dom->getAttribute('createdIn');
        $this->metadata->modified_in = $this->song_dom->getAttribute('modifiedIn');
        $this->metadata->modified_date = $this->song_dom->getAttribute('modifiedDate');
    }

    

    public function process_authors()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $authors = $properties->getElementsByTagName('author');
        foreach( $authors as $author)
        {
            $authorObject->value = $author->nodeValue;
            $authorObject->lang = $author->getAttribute('lang');
            $authorObject->type = $author->getAttribute('type');

            $this->properties->authors[] = $authorObject;
            unset($authorObject); 
        } 
    }
  

    private function process_copyright()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $copyright = $properties->getElementsByTagName('copyright')->item(0);
        $this->properties->copyright = $copyright->nodeValue;
    }
    
    private function process_ccli_number()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $ccli_number = $properties->getElementsByTagName('ccliNo')->item(0);
        $this->properties->ccli_number = $ccli_number->nodeValue;
    }

    private function process_verse_order()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $verse_order = $properties->getElementsByTagName('verseOrder')->item(0);
        $this->properties->verse_order = $verse_order->nodeValue;
    }

    private function process_titles() 
    {    
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $titles = $properties->getElementsByTagName('title');
        foreach( $titles as $title)
        {
            $titleObject->value = $title->nodeValue;
            $titleObject->lang = $title->getAttribute('lang');
            $titleObject->original = $title->getAttribute('original');

            $this->properties->titles[] = $titleObject;
            unset($titleObject); 
        }   
    } 

    private function process_lyrics()
    {
        $lyrics = $this->song_dom->getElementsByTagName('lyrics')->item(0);
        $verses = $lyrics->getElementsByTagName('verse');

        foreach($verses as $verse) { 

            
            $verseObject->name = $verse->getAttribute('name');
            $verseObject->lang = $verse->getAttribute('lang');
            $verseObject->translit = $verse->getAttribute('translit');

            $lines = $verse->getElementsByTagName('lines');

            foreach($lines as $line)
            {
                $line = $this->song_dom->ownerDocument->saveXML($line);
                 $line = str_replace("<br/>","\r\n",$line); 
                 $line = str_replace(['<lines>',"</lines>"],'',$line);
                 $verseObject->lines[] = $line;
                 
            }

            $this->lyrics->verses[] = $verseObject;
            unset($verseObject); 
            
            

            
        }
    }

    private function process_lines_mixed_content($lines)
    {
        return "";
    }
    
       

    private $song_xml;
    public $lyrics;
    public $metadata;
    public $properties;
    
}
