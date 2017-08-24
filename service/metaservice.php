<?php
/**
 * Nextcloud - Notes
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 */

namespace OCA\OpenLP\Service;

use OCA\OpenLP\Db\Song;
use OCA\OpenLP\Db\Meta;
use OCA\OpenLP\Db\MetaMapper;

/**
 * Class MetaService
 *
 * @package OCA\OpenLP\Service
 */
class MetaService {

	private $metaMapper;

	public function __construct(MetaMapper $metaMapper) {
		$this->metaMapper = $metaMapper;
	}

	public function updateAll($userId, Array $songs) {
		$metas = $this->metaMapper->getAll($userId);
		$metas = $this->getIndexedArray($metas, 'fileId');
		$songs = $this->getIndexedArray($songs, 'id');
		foreach($metas as $id=>$meta) {
			if(!array_key_exists($id, $songs)) {
				// DELETE obsolete songs
				$this->metaMapper->delete($meta);
				unset($metas[$id]);
			}
		}
		foreach($songs as $id=>$song) {
			if(!array_key_exists($id, $metas)) {
				// INSERT new songs
				$metas[$song->getId()] = $this->create($userId, $song);
			} elseif($song->getEtag()!==$metas[$id]->getEtag()) {
				// UPDATE changed songs
				$meta = $metas[$id];
				$this->updateIfNeeded($meta, $song);
			}
		}
		return $metas;
	}

	private function getIndexedArray(array $data, $property) {
		$property = ucfirst($property);
		$getter = 'get'.$property;
		$result = array();
		foreach($data as $entity) {
			$result[$entity->$getter()] = $entity;
		}
		return $result;
	}

	private function create($userId, $song) {
		$meta = Meta::fromSong($song, $userId);
		$this->metaMapper->insert($meta);
		return $meta;
	}

	private function updateIfNeeded(&$meta, $song) {
		if($song->getEtag()!==$meta->getEtag()) {
			$meta->setEtag($song->getEtag());
			$meta->setLastUpdate(time());
			$this->metaMapper->update($meta);
		}
	}
}
