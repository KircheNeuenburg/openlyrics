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

namespace OCA\OpenLyrics\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\IConfig;
use OCP\Files\IRootFolder;
use OCP\ITagManager;
use OCP\IDbConnection;


/**
 * Class SettingsController
 *
 * @package OCA\OpenLyrics\Controller
 */
class SettingsController extends Controller {
	
	private $userId;
	private $configManager;
	private $rootFolder;
    private $tagger;
    private $tagManager;
    private $db;
	public function __construct(
			$appName, 
			IRequest $request, 
			$userId, 
			IConfig $configManager,
            IDBConnection $db,
            ITagManager $tagManager,
            IRootFolder $rootFolder
			) {
		parent::__construct($appName, $request);
		$this->appName = $appName;
		$this->userId = $userId;
		$this->configManager = $configManager;
        $this->db = $db;
        $this->tagManager = $tagManager;
        $this->tagger = null;
        $this->rootFolder = $rootFolder;
	}


    /**
	 * @NoAdminRequired
	 */
	public function path($path) {
			try {
				$this->rootFolder->getUserFolder($this -> userId)->get($path);
			} catch (\OCP\Files\NotFoundException $e) {
				return new DataResponse(array('success' => false));
			}
			
			if ($path[0] !== '/') {
				$path = '/'.$path;
			}
			if ($path[strlen($path) - 1] !== '/') {
				$path .= '/';
			}
			$this->configManager->setUserValue($this->userId, $this->appName, 'path', $path);
		return new DataResponse(['success' => true]);
	}

}
