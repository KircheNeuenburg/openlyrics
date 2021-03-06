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

namespace OCA\OpenLyrics\Db;

use OCP\Files\File;
use OCP\Files\Folder;
use OCP\AppFramework\Db\Entity;

use OCA\OpenLyrics\Db\OpenLyrics;
/**
 * Class Song
 * @method integer getId()
 * @method void setId(integer $value)
 * @method string getEtag()
 * @method void setEtag(string $value)
 * @method integer getModified()
 * @method void setModified(integer $value)
 * @method string getTitle()
 * @method void setTitle(string $value)
 * @method string getCategory()
 * @method void setCategory(string $value)
 * @method string getContent()
 * @method void setContent(string $value)
 * @method string getMetadata()
 * @method void setMetadata()
 * @method string getProperties()
 * @method void setProperties(string $value)
 * @method string getLyrics()
 * @method void setLyrics(string $value)
 * @method boolean getFavorite()
 * @method void setFavorite(boolean $value)
 * @package OCA\OpenLyrics\Db
 */
class Song extends Entity {

    public $etag;
    public $modified;
    public $title;
    public $category;
    public $content;
    public $metadata;
    public $properties;
    public $lyrics;
    public $verse_order;
    public $favorite = false;

    public function __construct() {
        $this->addType('modified', 'integer');
        $this->addType('favorite', 'boolean');
    }

    /**
     * @param File $file
     * @return static
     */
    public static function fromFile(File $file, Folder $songsFolder, $tags=[],$content = true){
        $song = new static();
        
        $song->setId($file->getId());
        if($content == true)
        {
            $song->setContent(self::convertEncoding($file->getContent()));
            $openlyrics = new OpenLyrics($song->getContent());
            $song->openlyrics->metadata = $openlyrics->metadata;
            $song->openlyrics->properties = $openlyrics->properties;
        
            $song->openlyrics->lyrics = $openlyrics->lyrics;
            //$song->xml_output = $openlyrics->export_xml();
        }
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

    private function updateETag() {
        // collect all relevant attributes
        $data = '';
        foreach(get_object_vars($this) as $key => $val) {
            if($key!=='etag') {
                $data .= $val;
            }
        }
        $etag = md5($data);
        $this->setEtag($etag);
    }
}
