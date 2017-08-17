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

namespace OCA\OpenLP\Controller;

use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

use OCA\Notes\Service\NotesService;
use OCA\Notes\Service\MetaService;
use OCA\Notes\Db\Note;

/**
 * Class NotesApiController
 *
 * @package OCA\Notes\Controller
 */
class NotesApiController extends ApiController {

    use Errors;

    /** @var NotesService */
    private $service;
    /** @var MetaService */
    private $metaService;
    /** @var string */
    private $userId;

    /**
     * @param string $AppName
     * @param IRequest $request
     * @param NotesService $service
     * @param string $UserId
     */
    public function __construct($AppName, IRequest $request, OpenLPService $service, MetaService $metaService, $UserId) {
        parent::__construct($AppName, $request);
        $this->service = $service;
        $this->metaService = $metaService;
        $this->userId = $UserId;
    }


    /**
     * @param Song $song
     * @param string[] $exclude the fields that should be removed from the
     * songs
     * @return Song
     */
    private function excludeFields(Song &$song, array $exclude) {
        if(count($exclude) > 0) {
            foreach ($exclude as $field) {
                if(property_exists($song, $field)) {
                    unset($song->$field);
                }
            }
        }
        return $song;
    }


    /**
     * @NoAdminRequired
     * @CORS
     * @NoCSRFRequired
     *
     * @param string $exclude
     * @return DataResponse
     */
    public function index($exclude='', $pruneBefore=0) {
        $exclude = explode(',', $exclude);
        $now = new \DateTime(); // this must be before loading songs if there are concurrent changes possible
        $songs = $this->service->getAll($this->userId);
        foreach ($songs as $song) {
            $lastUpdate = $metas[$song->getId()]->getLastUpdate();
            if($pruneBefore && $lastUpdate<$pruneBefore) {
                $vars = get_object_vars($song);
                unset($vars['id']);
                $this->excludeFields($song, array_keys($vars));
            } else {
                $this->excludeFields($song, $exclude);
            }
        }
        $etag = md5(json_encode($songs));
        if ($this->request->getHeader('If-None-Match') === '"'.$etag.'"') {
            return new DataResponse([], Http::STATUS_NOT_MODIFIED);
        }
        return (new DataResponse($songs))
            ->setLastModified($now)
            ->setETag($etag);
    }


    /**
     * @NoAdminRequired
     * @CORS
     * @NoCSRFRequired
     *
     * @param int $id
     * @param string $exclude
     * @return DataResponse
     */
    public function get($id, $exclude='') {
        $exclude = explode(',', $exclude);

        return $this->respond(function () use ($id, $exclude) {
            $song = $this->service->get($id, $this->userId);
            $song = $this->excludeFields($song, $exclude);
            return $song;
        });
    }


    /**
     * @NoAdminRequired
     * @CORS
     * @NoCSRFRequired
     *
     * @param string $content
     * @param int $modified
     * @return DataResponse
     */
    public function create($content, $modified=0) {
        return $this->respond(function () use ($content, $modified) {
            $song = $this->service->create($this->userId);
            return $this->updateData($song->getId(), $content,  $modified);
        });
    }


    /**
     * @NoAdminRequired
     * @CORS
     * @NoCSRFRequired
     *
     * @param int $id
     * @param string $content
     * @param int $modified
     * @return DataResponse
     */
    public function update($id, $content=null, $modified=0) {
        return $this->respond(function () use ($id, $content,  $modified) {
            return $this->updateData($id, $content, $modified);
        });
    }

    /**
     * Updates a song, used by create and update
     * @param int $id
     * @param string $content
     * @param int $modified
     * @return Song
     */
    private function updateData($id, $content, $modified) {
        if($content===null) {
            return $this->service->get($id, $this->userId);
        } else {
            return $this->service->update($id, $content, $this->userId, $modified);
        }
    }

    /**
     * @NoAdminRequired
     * @CORS
     * @NoCSRFRequired
     *
     * @param int $id
     * @return DataResponse
     */
    public function destroy($id) {
        return $this->respond(function () use ($id) {
            $this->service->delete($id, $this->userId);
            return [];
        });
    }


}
