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
        if($xml_content !== '')
        {
        
            $song_dom = new DOMDocument('1.0', 'utf-8');
            $song_dom->loadXML($xml_content);
            $this->song_dom = $song_dom->getElementsByTagName('song')->item(0);

            $this->process_metadata();
            $this->process_properties();
            $this->process_lyrics();
        }
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

        

        $song->appendChild($this->export_properties($dom));
        $song->appendChild($this->export_lyrics($dom));
        $dom->appendChild($song);
        $dom->formatOutput = true;
        
        return $dom->saveXML();
    }

    private function export_properties(DOMDocument $dom)
    {
        $properties = $dom->createElement('properties');

        $properties->appendChild($this->export_titles($dom));

        $properties->appendChild($this->export_authors($dom));

        if($this->properties->copyright!= '')
        {
            $properties->appendChild($this->export_copyright($dom));
        }
        if($this->properties->ccli_number != '')
        {
            $properties->appendChild($this->export_ccli_number($dom));
        }
        if($this->properties->release_date != '')
        {
            $properties->appendChild($this->export_release_date($dom));
        }
        if($this->properties->transposition != '')
        {
            $properties->appendChild($this->export_transposition($dom));
        }
        if($this->properties->tempo != '')
        {
            $properties->appendChild($this->export_tempo($dom));
        }
        if($this->properties->key != '')
        {
            $properties->appendChild($this->export_key($dom));
        }
        if($this->properties->variant != '')
        {
            $properties->appendChild($this->export_variant($dom));
        }
        if($this->properties->publisher != '')
        {
            $properties->appendChild($this->export_publisher($dom));
        }
        if($this->properties->version != '')
        {
            $properties->appendChild($this->export_version($dom));
        }
        if($this->properties->keywords != '')
        {
            $properties->appendChild($this->export_keywords($dom));
        }
        if($this->properties->verse_order != '')
        {
            $properties->appendChild($this->export_verse_order($dom));
        }
        $properties->appendChild($this->export_songbooks($dom));
        $properties->appendChild($this->export_themes($dom));
        $properties->appendChild($this->export_comments($dom));
        
        return $properties;
    }

    private function export_titles(DOMDocument $dom)
    {
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
        return $titles;
    }

    private function export_authors(DOMDocument $dom)
    {
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
        return $authors;
    }

    private function export_copyright(DOMDocument $dom)
    {
        return $dom->createElement('copyright',$this->properties->copyright);
    }

    private function export_ccli_number(DOMDocument $dom)
    {
        return $dom->createElement('ccliNo',$this->properties->ccli_number);
    }

    private function export_release_date(DOMDocument $dom)
    {
        return $dom->createElement('released',$this->properties->release_date);
    }

    private function export_transposition(DOMDocument $dom)
    {
        return $dom->createElement('transposition',$this->properties->transposition);
    }

    private function export_tempo(DOMDocument $dom)
    {
        $tempo = $dom->createElement('tempo',$this->properties->tempo->value);
        $tempo->setAttribute('type',$this->properties->tempo->type);
        return $tempo;
    }
    private function export_key(DOMDocument $dom)
    {
        return $dom->createElement('key',$this->properties->key);
    }
    private function export_variant(DOMDocument $dom)
    {
        return $dom->createElement('variant',$this->properties->variant);
    }

    private function export_publisher(DOMDocument $dom)
    {
        return $dom->createElement('publisher',$this->properties->publisher);
    }

    private function export_version(DOMDocument $dom)
    {
        return $dom->createElement('version',$this->properties->version);
    }

    private function export_keywords(DOMDocument $dom)
    {
        return $dom->createElement('keywords',$this->properties->keywords);
    }

    private function export_verse_order(DOMDocument $dom)
    {
        return $dom->createElement('verseOrder',$this->properties->verse_order);
    }

    private function export_songbooks(DOMDocument $dom)
    {
        $songbooks = $dom->createElement('songbooks');
        foreach($this->properties->songbooks as $songbook)
        {
            $songbook_el = $dom->createElement('songbook');
            if($songbook->name != '')
            {
                $attr_name = $dom->createAttribute('name');
                $attr_name->value = $songbook->name;
                $songbook_el->appendChild($attr_name);
            }
            if($songbook->entry != '')
            {
                $attr_entry = $dom->createAttribute('entry');
                $attr_entry->value = $songbook->entry;
                $songbook_el->appendChild($attr_entry);
            }
            $songbooks->appendChild($songbook_el);
        }
        return $songbooks;
    }

    private function export_themes(DOMDocument $dom)
    {
        $themes = $dom->createElement('themes');
        foreach($this->properties->themes as $theme)
        {
            $theme_el = $dom->createElement('theme',$theme->value);
            if($theme->lang != '')
            {
                $attr_lang = $dom->createAttribute('lang');
                $attr_lang->value = $theme->lang;
                $theme_el->appendChild($attr_lang);
            }
            $themes->appendChild($theme_el);
        }
        return $themes;
    }

    private function export_comments(DOMDocument $dom)
    {
        $comments = $dom->createElement('comments');
        foreach($this->properties->comments as $comment)
        {
            $comment_el = $dom->createElement('comment',$comment);
            $comments->appendChild($comment_el);
        }
        return $comments;
    }

    private function export_lyrics(DOMDocument $dom)
    {
        $lyrics =  $dom->createElement('lyrics');
        foreach($this->lyrics->verses as $verse)
        {
            $verse_el = $dom->createElement('verse');
            if($verse->name != '')
            {
                $attr_name = $dom->createAttribute('name');
                $attr_name->value = $verse->name;
                $verse_el->appendChild($attr_name);
            }
            if($verse->lang != '')
            {
                $attr_lang = $dom->createAttribute('lang');
                $attr_lang->value = $verse->lang;
                $verse_el->appendChild($attr_lang);
            }
            if($verse->translit != '')
            {
                $attr_translit = $dom->createAttribute('translit');
                $attr_translit->value = $verse->translit;
                $verse_el->appendChild($attr_translit);
            }
            foreach($verse->lines as $line)
            {
                $verse_el->appendChild($this->export_lines($dom,$line));
            }
            
            $lyrics->appendChild($verse_el);
        }
        return $lyrics;
    }

    private function export_lines(DOMDocument $dom, $line)
    {
        preg_match_all('/\{(\w+)\}/',$line,$start_tags);
        preg_match_all('/\{\/(\w+)\}/',$line,$end_tags);
        $text = $line;
        foreach($start_tags[1] as $tag)
        {         
            if(in_array($tag, $end_tags[1]))
            {
                        
                $text = str_replace('{'.$tag.'}','<tag name="'.$tag.'">',$text);
            }
            else
            {
                $text = str_replace('{'.$tag.'}','<tag name="'.$tag.'"/>',$text);
            }           
        }
        foreach($end_tags[1] as $tag)
        {       
            $text = str_replace('{/'.$tag.'}','</tag>',$text);
        }

        $text = preg_replace("/\r\n|\r|\n/", "<br />", $text);

        $text = preg_replace('/\[(\w.*?)\]/','<chord name="${1}"/>',$text);
        $tmp_dom = new DOMDocument('1.0', 'utf-8');
        $tmp_dom->loadXML('<lines>'.$text.'</lines>');
                                
        $lines = $dom->importNode($tmp_dom->getElementsByTagName('lines')->item(0),true);
                

        return $lines;
    }
    
    private function process_metadata()
    {
        $this->metadata->version = $this->song_dom->getAttribute('version');
        $this->metadata->created_in = $this->song_dom->getAttribute('createdIn');
        $this->metadata->modified_in = $this->song_dom->getAttribute('modifiedIn');
        $this->metadata->modified_date = $this->song_dom->getAttribute('modifiedDate');
    }

    private function process_properties()
    {
        $this->process_titles();
        $this->process_authors();
        $this->process_copyright();
        $this->process_ccli_number();
        $this->process_release_date();
        $this->process_transposition();
        $this->process_tempo();
        $this->process_key();
        $this->process_variant();
        $this->process_publisher();
        $this->process_version();
        $this->process_keywords();
        $this->process_verse_order();
        $this->process_songbooks() ;
        $this->process_themes() ;
        $this->process_comments(); 
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
        if( empty($this->properties->titles))
        {
            $titleObject->value = '';
            $titleObject->lang = '';
            $titleObject->original = '';

            $this->properties->titles[] = $titleObject;
        }   
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
        if( empty($this->properties->authors))
        {
            $authorObject->value = '';
            $authorObject->lang = '';
            $authorObject->type = '';

            $this->properties->authors[] = $authorObject;
        }
    }
  
    private function process_copyright()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $copyright = $properties->getElementsByTagName('copyright')->item(0);
        $this->properties->copyright = $copyright->nodeValue;
        if( empty($this->properties->copyright))
        {
            $this->properties->copyright = '';
        }
    }
    
    private function process_ccli_number()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $ccli_number = $properties->getElementsByTagName('ccliNo')->item(0);
        $this->properties->ccli_number = $ccli_number->nodeValue;
        if( empty($this->properties->ccli_number))
        {
            $this->properties->ccli_number = '';
        }
    }

    private function process_release_date()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $release_date = $properties->getElementsByTagName('released')->item(0);
        $this->properties->release_date = $release_date->nodeValue;
        if( empty($this->properties->release_date))
        {
            $this->properties->release_date = '';
        }
    }

    private function process_transposition()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $transposition = $properties->getElementsByTagName('transposition')->item(0);
        $this->properties->transposition = $transposition->nodeValue;
        if( empty($this->properties->transposition))
        {
            $this->properties->transposition = '';
        }
    }

    private function process_tempo()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $tempo = $properties->getElementsByTagName('tempo')->item(0);
        if($tempo)
        {
            $this->properties->tempo->value = $tempo->nodeValue;
            $this->properties->tempo->type = $tempo->getAttribute('type');
        }
        else
        {
            $this->properties->tempo->value = '';
            $this->properties->tempo->type = '';
        }

    }

    private function process_key()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $key = $properties->getElementsByTagName('key')->item(0);
        $this->properties->key = $key->nodeValue;
        if( empty($this->properties->authors))
        {
            $this->properties->copyright = '';
        }
    }

    private function process_variant()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $variant = $properties->getElementsByTagName('variant')->item(0);
        $this->properties->variant = $variant->nodeValue;
        if( empty($this->properties->variant))
        {
            $this->properties->variant = '';
        }
    }

    private function process_publisher()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $publisher = $properties->getElementsByTagName('publisher')->item(0);
        $this->properties->publisher = $publisher->nodeValue;
        if( empty($this->properties->publisher))
        {
            $this->properties->publisher = '';
        }
    }

    private function process_version()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $version = $properties->getElementsByTagName('version')->item(0);
        $this->properties->version = $version->nodeValue;
        if( empty($this->properties->version))
        {
            $this->properties->version = '';
        }
    }

    private function process_keywords()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $keywords = $properties->getElementsByTagName('keywords')->item(0);
        $this->properties->keywords = $keywords->nodeValue;
        if( empty($this->properties->keywords))
        {
            $this->properties->keywords = '';
        }
    }

    private function process_verse_order()
    {
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $verse_order = $properties->getElementsByTagName('verseOrder')->item(0);
        $this->properties->verse_order = $verse_order->nodeValue;
        if( empty($this->properties->verse_order))
        {
            $this->properties->verse_order = '';
        }
    }

    private function process_songbooks() 
    {    
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $songbooks = $properties->getElementsByTagName('songbook');
        foreach( $songbooks as $songbook)
        {
            $songbookObject->name = $songbook->getAttribute('name');
            $songbookObject->entry = $songbook->getAttribute('entry');

            $this->properties->songbooks[] = $songbookObject;
            unset($songbookObject); 
        }
        if( empty($this->properties->songbooks))
        {
            $songbookObject->name = '';
            $songbookObject->entry = '';

            $this->properties->songbooks[] = $songbookObject;   
        }
    }

    private function process_themes() 
    {    
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $themes = $properties->getElementsByTagName('theme');
        foreach( $themes as $theme)
        {
            $themeObject->value = $theme->nodeValue;
            $themeObject->lang = $theme->getAttribute('lang');

            $this->properties->themes[] = $themeObject;
            unset($themeObject); 
        }   
        if( empty($this->properties->songbooks))
        {
            $themeObject->value = '';
            $themeObject->lang = '';

            $this->properties->themes[] = $themeObject;
        }
    } 

    private function process_comments() 
    {    
        $properties = $this->song_dom->getElementsByTagName('properties')->item(0);
        $comments = $properties->getElementsByTagName('comment');
        foreach( $comments as $comment)
        {
            $this->properties->comments[] = $comment->nodeValue;
        }   
        if( empty($this->properties->comments))
        {
            $this->properties->comments[] = '';
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
                $line_text = $this->process_lines_mixed_content($line);
                if($line->getAttribute('break')=== 'optional')
                {
                    $line_text .= "\r\n".'[---]';
                }
                $verseObject->lines[] = $line_text;
                 
            }

            $this->lyrics->verses[] = $verseObject;
            unset($verseObject); 
        }
        if(empty($this->lyrics->verses))
        {
            $verseObject->name = '';
            $verseObject->lang = '';
            $verseObject->translit = '';
            $verseObject->lines[] = '';
            $this->lyrics->verses[] = $verseObject;
        }
    }

    private function process_lines_mixed_content($line_children)
    {
        $text = '';

        foreach($line_children->childNodes as $child)
        {
            $use_endtag = true;
            $use_endcomment = false;

            if($child->nodeName === 'comment')
            {
                $use_endcomment = true;
            }
            elseif($child->nodeName === 'chord')
            {
                $text .= '['.$child->getAttribute('name').']';
            }
            elseif($child->nodeName === 'br')
            {
                $text .= "\r\n";
            }
            elseif($child->nodeName === 'tag')
            {
                $text .= '{'.$child->getAttribute('name').'}';
                if(!$child->firstChild and $child->textContent === '')
                {
                    $use_endtag = false;
                }
            }
            elseif($child->nodeName === '#text')
            {
                $text .= $child->textContent;
            }
            if($use_endcomment == false)
            {
                $text .= $this->process_lines_mixed_content($child);
            }
            if($child->nodeName === 'tag' and $use_endtag)
            {
                $text .= '{/'.$child->getAttribute('name').'}';
                $use_endtag = true;
            }    
        }
        return $text;
    }
    
       

    private $song_xml;
    public $lyrics;
    public $metadata;
    public $properties;
    
}
