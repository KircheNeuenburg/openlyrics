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

namespace OCA\OpenLP\Service;

use OCP\Files\FileInfo;
use OCP\IL10N;
use OCP\Files\IRootFolder;
use OCP\Files\Folder;
use OCP\ILogger;

use OCA\OpenLP\Db\Song;

/**
 * Class SongsService
 *
 * @package OCA\OpenLP\Service
 */
class SongsService {

    private $l10n;
    private $root;
    private $logger;
    private $appName;

    /**
     * @param IRootFolder $root
     * @param IL10N $l10n
     * @param ILogger $logger
     * @param String $appName
     */
    public function __construct (IRootFolder $root, IL10N $l10n, ILogger $logger, $appName) {
        $this->root = $root;
        $this->l10n = $l10n;
        $this->logger = $logger;
        $this->appName = $appName;
    }


    /**
     * @param string $userId
     * @return array with all songs in the current directory
     */
    public function getAll ($userId){
        $songsFolder = $this->getFolderForUser($userId);
        $songs = $this->gatherSongFiles($songsFolder);
        $filesById = [];
        foreach($songs as $song) {
            $filesById[$song->getId()] = $song;
        }
        $tagger = \OC::$server->getTagManager()->load('files');
        if($tagger===null) {
            $tags = [];
        } else {
            $tags = $tagger->getTagsForObjects(array_keys($filesById));
        }

        $songs = [];
        foreach($filesById as $id=>$file) {
            $songs[] = Song::fromFile($file, $songsFolder, array_key_exists($id, $tags) ? $tags[$id] : []);
        }

        return $songs;
    }


    /**
     * Used to get a single song by id
     * @param int $id the id of the song to get
     * @param string $userId
     * @throws SongDoesNotExistException if song does not exist
     * @return Song
     */
    public function get ($id, $userId) {
        $folder = $this->getFolderForUser($userId);
        return Song::fromFile($this->getFileById($folder, $id), $folder, $this->getTags($id));
    }

    private function getTags ($id) {
        $tagger = \OC::$server->getTagManager()->load('files');
        if($tagger===null) {
            $tags = [];
        } else {
            $tags = $tagger->getTagsForObjects([$id]);
        }
        return array_key_exists($id, $tags) ? $tags[$id] : [];
    }

    /**
     * Creates a song and returns the empty song
     * @param string $userId
     * @see update for setting song content
     * @return Song the newly created song
     */
    public function create ($userId) {
        $title = $this->l10n->t('New song');
        $folder = $this->getFolderForUser($userId);

        // check new song exists already and we need to number it
        // pass -1 because no file has id -1 and that will ensure
        // to only return filenames that dont yet exist
        $path = $this->generateFileName($folder, $title, "xml", -1);
        $file = $folder->newFile($path);

        return Song::fromFile($file, $folder);
    }


    /**
     * Updates a song. Be sure to check the returned song since the title is
     * dynamically generated and filename conflicts are resolved
     * @param int $id the id of the song used to update
     * @param string $content the content which will be written into the song
     * the title is generated from the first line of the content
     * @param int $mtime time of the song modification (optional)
     * @throws SongDoesNotExistException if song does not exist
     * @return \OCA\OpenLP\Db\Song the updated song
     */
    public function update ($id, $content, $userId, $category=null, $mtime=0) {
        $songsFolder = $this->getFolderForUser($userId);
        $file = $this->getFileById($songsFolder, $id);
        $folder = $file->getParent();
        $title = $this->getSafeTitleFromContent($content);


        // rename/move file with respect to title/category
        // this can fail if access rights are not sufficient or category name is illegal
        try {
            $currentFilePath = $file->getPath();
            $fileExtension = pathinfo($file->getName(), PATHINFO_EXTENSION);

            // detect (new) folder path based on category name
            if($category===null) {
                $basePath = pathinfo($file->getPath(), PATHINFO_DIRNAME);
            } else {
                $basePath = $songsFolder->getPath();
                if(!empty($category))
                    $basePath .= '/'.$category;
                $this->getOrCreateFolder($basePath);
            }

            // assemble new file path
            $newFilePath = $basePath . '/' . $this->generateFileName($folder, $title, $fileExtension, $id);

            // if the current path is not the new path, the file has to be renamed
            if($currentFilePath !== $newFilePath) {
                $file->move($newFilePath);
            }
        } catch(\OCP\Files\NotPermittedException $e) {
            $this->logger->error('Moving this song to the desired target is not allowed. Please check the song\'s target category.', array('app' => $this->appName));
        }

        $file->putContent($content);

        if($mtime) {
            $file->touch($mtime);
        }

        return Song::fromFile($file, $songsFolder, $this->getTags($id));
    }


    /**
     * Set or unset a song as favorite.
     * @param int $id the id of the song used to update
     * @param boolean $favorite whether the song should be a favorite or not
     * @throws SongDoesNotExistException if song does not exist
     * @return boolean the new favorite state of the song
     */
    public function favorite ($id, $favorite, $userId){
        $folder = $this->getFolderForUser($userId);
        $file = $this->getFileById($folder, $id);
        if(!$this->isSong($file)) {
            throw new SongDoesNotExistException();
        }
        $tagger = \OC::$server->getTagManager()->load('files');
        if($favorite)
            $tagger->addToFavorites($id);
        else
            $tagger->removeFromFavorites($id);

        $tags = $tagger->getTagsForObjects([$id]);
        return array_key_exists($id, $tags) && in_array(\OC\Tags::TAG_FAVORITE, $tags[$id]);
    }


    /**
     * Deletes a song
     * @param int $id the id of the song which should be deleted
     * @param string $userId
     * @throws SongDoesNotExistException if song does not
     * exist
     */
    public function delete ($id, $userId) {
        $folder = $this->getFolderForUser($userId);
        $file = $this->getFileById($folder, $id);
        $file->delete();
    }

    private function getSafeTitleFromContent($content) {
        // prepare content: remove markdown characters and empty spaces
        $content = preg_replace("/^\s*[*+-]\s+/mu", "", $content); // list item
        $content = preg_replace("/^#+\s+(.*?)\s*#*$/mu", "$1", $content); // headline
        $content = preg_replace("/^(=+|-+)$/mu", "", $content); // separate line for headline
        $content = preg_replace("/(\*+|_+)(.*?)\\1/mu", "$2", $content); // emphasis
        $content = trim($content);

        // generate content from the first line of the title
        $splitContent = preg_split("/\R/u", $content, 2);
        $title = trim($splitContent[0]);

        // ensure that title is not empty
        if(empty($title)) {
            $title = $this->l10n->t('New song');
        }

        // prevent directory traversal
        $title = str_replace(array('/', '\\'), '',  $title);

        // using a maximum of 100 chars should be enough
        $title = mb_substr($title, 0, 100, "UTF-8");

        return $title;
    }

    /**
     * @param Folder $folder
     * @param int $id
     * @throws SongDoesNotExistException
     * @return \OCP\Files\File
     */
    private function getFileById ($folder, $id) {
        $file = $folder->getById($id);

        if(count($file) <= 0 || !$this->isSong($file[0])) {
            throw new SongDoesNotExistException();
        }
        return $file[0];
    }


    /**
     * @param string $userId the user id
     * @return Folder
     */
    private function getFolderForUser ($userId) {
        $path = '/' . $userId . '/files/Songs';
        return $this->getOrCreateFolder($path);
    }


    /**
     * Finds a folder and creates it if non-existent
     * @param string $path path to the folder
     * @return Folder
     */
    private function getOrCreateFolder($path) {
        if ($this->root->nodeExists($path)) {
            $folder = $this->root->get($path);
        } else {
            $folder = $this->root->newFolder($path);
        }
        return $folder;
    }


    /**
     * get path of file and the title.txt and check if they are the same
     * file. If not the title needs to be renamed
     *
     * @param Folder $folder a folder to the songs directory
     * @param string $title the filename which should be used
     * @param string $extension the extension which should be used
     * @param int $id the id of the song for which the title should be generated
     * used to see if the file itself has the title and not a different file for
     * checking for filename collisions
     * @return string the resolved filename to prevent overwriting different
     * files with the same title
     */
    private function generateFileName (Folder $folder, $title, $extension, $id) {
        $path = $title . '.' . $extension;

        // if file does not exist, that name has not been taken. Similar we don't
        // need to handle file collisions if it is the filename did not change
        if (!$folder->nodeExists($path) || $folder->get($path)->getId() === $id) {
            return $path;
        } else {
            // increments name (2) to name (3)
            $match = preg_match('/\((?P<id>\d+)\)$/u', $title, $matches);
            if($match) {
                $newId = ((int) $matches['id']) + 1;
                $newTitle = preg_replace('/(.*)\s\((\d+)\)$/u',
                    '$1 (' . $newId . ')', $title);
            } else {
                $newTitle = $title . ' (2)';
            }
            return $this->generateFileName($folder, $newTitle, $extension, $id);
        }
    }

	/**
	 * gather song files in given directory and all subdirectories
	 * @param Folder $folder
	 * @return array
	 */
	private function gatherSongFiles ($folder) {
		$songs = [];
		$nodes = $folder->getDirectoryListing();
		foreach($nodes as $node) {
			if($node->getType() === FileInfo::TYPE_FOLDER) {
				$songs = array_merge($songs, $this->gatherSongFiles($node));
				continue;
			}
			if($this->isSong($node)) {
				$songs[] = $node;
			}
		}
		return $songs;
	}


    /**
     * test if file is a song
     *
     * @param \OCP\Files\File $file
     * @return bool
     */
    private function isSong($file) {
        $allowedExtensions = ['xml'];

        if($file->getType() !== 'file') return false;
        if(!in_array(
            pathinfo($file->getName(), PATHINFO_EXTENSION),
            $allowedExtensions
        )) return false;

        return true;
    }

}
