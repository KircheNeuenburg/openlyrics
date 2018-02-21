<?php
/**
 * Nextcloud - Notes
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 */

namespace OCA\OpenLyrics\Db;

use OCP\AppFramework\Db\Entity;

class Meta extends Entity {

	public $userId;
	public $fileId;
	public $lastUpdate;
	public $etag;

	/**
	 * @param Song $song
	 * @return static
	 */
	public static function fromSong(Song $song, $userId) {
		$meta = new static();
		$meta->setUserId($userId);
		$meta->setFileId($song->getId());
		$meta->setLastUpdate(time());
		$meta->setEtag($song->getEtag());
		return $meta;
	}
}
