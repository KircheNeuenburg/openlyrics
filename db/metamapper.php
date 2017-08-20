<?php
/**
 * Nextcloud - Notes
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 */

namespace OCA\OpenLP\Db;

use OCP\IDBConnection;
use OCP\AppFramework\Db\Mapper;

class MetaMapper extends Mapper {

	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'openlp_songs_meta');
	}

	public function getAll($userId) {
		$sql = 'SELECT * FROM `*PREFIX*openlp_songs_meta` WHERE user_id=?';
		return $this->findEntities($sql, [$userId]);
	}

	public function get($userId, $fileId) {
		$sql = 'SELECT * FROM `*PREFIX*openlp_songs_meta` WHERE user_id=? AND file_id=?';
		return $this->findEntity($sql, [$userId, $fileId]);
	}
}
