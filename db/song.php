<?php
/**
 * Nextcloud - OpenLP
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author David Lang
 * @copyright David Lang 2017
 */

namespace OCA\OpenLP\Db;

use OCP\Files\File;
use OCP\Files\Folder;
use OCP\AppFramework\Db\Entity;

/**
 * Class Song
 * @method integer getId()
 * @method void setId(integer $value)
 * @method integer getModified()
 * @method void setModified(integer $value)
 * @method string getTitle()
 * @method void setTitle(string $value)
 * @method string getContent()
 * @method void setContent(string $value)
 * @package OCA\Songs\Db
 */
class Song extends Entity {

    public $modified;
    public $title;
    public $content;

    public function __construct() {
        $this->addType('modified', 'integer');
        $this->addType('favorite', 'boolean');
    }

    /**
     * @param File $file
     * @return static
     */
    public static function fromFile(File $file, Folder $songFolder){
        $song = new static();
        $song->setId($file->getId());
        $song->setContent(self::convertEncoding($file->getContent()));
        $song->setModified($file->getMTime());
        //$song->setTitle(pathinfo($file->getName(),PATHINFO_FILENAME)); // remove extension
        $song->resetUpdatedFields();
        return $song;
    }

    private static function convertEncoding($str) {
        if(!mb_check_encoding($str, 'UTF-8')) {
            $str = mb_convert_encoding($str, 'UTF-8');
        }
        return $str;
    }

}
