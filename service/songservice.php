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

namespace OCA\OpenLP\Service;

use OCP\Files\FileInfo;
use OCP\IL10N;
use OCP\Files\IRootFolder;
use OCP\Files\Folder;
use OCP\ILogger;

use OCA\OpenLP\Db\Song;

/**
 * Class OpenLPService
 *
 * @package OCA\OpenLP\Service
 */
class OpenLPService {

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
        $songFolder = $this->getFolderForUser($userId);
        $songs = $this->gatherSongFiles($songFolder);
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
            $songs[] = Song::fromFile($file, $songFolder, array_key_exists($id, $tags) ? $tags[$id] : []);
        }

        return $songs;
    }


    /**
     * Used to get a single song by id
     * @param int $id the id of the note to get
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
     * @see update for setting note content
     * @return Note the newly created note
     */
    public function create ($userId) {
        $title = $this->l10n->t('New song');
        $folder = $this->getFolderForUser($userId);

        // check new note exists already and we need to number it
        // pass -1 because no file has id -1 and that will ensure
        // to only return filenames that dont yet exist
        $path = $this->generateFileName($folder, $title, "txt", -1);
        $file = $folder->newFile($path);

        return Note::fromFile($file, $folder);
    }


    /**
     * Updates a song. Be sure to check the returned note since the title is
     * dynamically generated and filename conflicts are resolved
     * @param int $id the id of the note used to update
     * @param string $content the content which will be written into the note
     * the title is generated from the first line of the content
     * @param int $mtime time of the note modification (optional)
     * @throws NoteDoesNotExistException if note does not exist
     * @return \OCA\Notes\Db\Note the updated note
     */
    public function update ($id, $content, $userId, $category=null, $mtime=0) {
        $songFolder = $this->getFolderForUser($userId);
        $file = $this->getFileById($notesFolder, $id);
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
                $basePath = $songFolder->getPath();
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
            $this->logger->error('Moving this song to the desired target is not allowed. Please check the note\'s target category.', array('app' => $this->appName));
        }

        $file->putContent($content);

        if($mtime) {
            $file->touch($mtime);
        }

        return Song::fromFile($file, $songFolder, $this->getTags($id));
    }


    /**
     * Deletes a song
     * @param int $id the id of the song which should be deleted
     * @param string $userId
     * @throws SongDoesNotExistException if song does not exist
     */
    public function delete ($id, $userId) {
        $folder = $this->getFolderForUser($userId);
        $file = $this->getFileById($folder, $id);
        $file->delete();
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
        $path = '/' . $userId . '/files/OpenLP/Songs';
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
	 * gather song files in given directory and all subdirectories
	 * @param Folder $folder
	 * @return array
	 */
	private function gatherSongFiles ($folder) {
		$songs = [];
		$nodes = $folder->getDirectoryListing();
		foreach($nodes as $node) {
			if($node->getType() === FileInfo::TYPE_FOLDER) {
				$songs = array_merge($songs, $this->gatherNoteFiles($node));
				continue;
			}
			if($this->isSong($node)) {
				$songs[] = $node;
			}
		}
		return $songs;
	}


    /**
     * test if file is a note
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

        $xml = simplexml_load_file($file);
        if(!$xml)
            return false;
        if(!isset($xml->Song))
            return false;
        return true;
    }

}
