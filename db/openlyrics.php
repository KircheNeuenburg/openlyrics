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

    private static function convertEncoding($str) {
        if(!mb_check_encoding($str, 'UTF-8')) {
            $str = mb_convert_encoding($str, 'UTF-8');
        }
        return $str;
    
    }

    public static function getMetadata($content) {
        $xml = simplexml_load_string($content);
        $metadata; 
        
        if(isset($xml['version']))
        {
            $metadata->version = $xml['version']->__toString();
        }
        else
        {
            $metadata->version = '';
        }

        if(isset($xml['createdIn']))
        {
            $metadata->createdIn = $xml['createdIn']->__toString();
        }
        else
        {
            $metadata->createdIn = '';
        }

        if(isset($xml['modifiedIn']))
        {
            $metadata->modifiedIn = $xml['modifiedIn']->__toString();
        }
        else
        {
            $metadata->modifiedIn = '';
        }

        if(isset($xml['modifiedDate']))
        {
            $metadata->modifiedDate = $xml['modifiedDate']->__toString();
        }
        else
        {
            $metadata->modifiedDate = '';
        }
        return $metadata;
        
    } 

    public static function getTitles($content) {
        $xml = simplexml_load_string($content);
        $titles = []; 
        foreach($xml->properties->titles->title as $title) { 
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
            $titles[] = $titleObject;
            unset($titleObject);            
        }
        return $titles;
        
    } 


    public static function getAuthors($content) {
        $xml = simplexml_load_string($content);
        $authors = []; 
        foreach($xml->properties->authors->author as $author) { 
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
            $authors[] = $authorObject;
            unset($authorObject);            
        }
        return $authors;
    } 

    public static function getVerses($content) {
        $xml = simplexml_load_string($content);
        $verses = []; 
        foreach($xml->lyrics->verse as $verse) { 
            $verseObject;
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
            foreach($verse->lines as $lines) 
            {
                $verseObject->lines[] = $lines->__toString();
            }
                
            $verses[] = $verseObject;
            unset($verseObject);            
        }
        return $verses;
    } 

}
