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

namespace OCA\OpenLP\Controller;

use OCP\AppFramework\Controller;
use OCP\IRequest;
use OCP\IConfig;
use OCP\AppFramework\Http\DataResponse;

use OCA\OpenLP\Service\SongsService;

/**
 * Class SongsController
 *
 * @package OCA\OpenLP\Controller
 */
class SongsController extends Controller {

    use Errors;

    /** @var SongsService */
    private $songsService;
    /** @var IConfig */
    private $settings;
    /** @var string */
    private $userId;

    /**
     * @param string $AppName
     * @param IRequest $request
     * @param SongsService $service
     * @param IConfig $settings
     * @param string $UserId
     */
    public function __construct($AppName, IRequest $request,
                                SongsService $service, IConfig $settings,
                                $UserId){
        parent::__construct($AppName, $request);
        $this->songsService = $service;
        $this->settings = $settings;
        $this->userId = $UserId;
    }


    /**
     * @NoAdminRequired
     */
    public function index() {
        return new DataResponse($this->songsService->getAll($this->userId));
    }


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param int $id
     * @return DataResponse
     */
    public function get($id) {
        // save the last viewed song
        $this->settings->setUserValue(
            $this->userId, $this->appName, 'songsLastViewedSong', $id
        );

        return $this->respond(function ()  use ($id) {
            return $this->songsService->get($id, $this->userId);
        });
    }


    /**
     * @NoAdminRequired
     *
     * @param string $content
     */
    public function create($content="") {
        $song = $this->songsService->create($this->userId);
        $song = $this->songsService->update(
            $song->getId(), $content, $this->userId
        );
        return new DataResponse($song);
    }


    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param string $content
     * @return DataResponse
     */
    public function update($id, $content) {
        return $this->respond(function () use ($id, $content) {
            return $this->songsService->update($id, $content, $this->userId);
        });
    }


    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param boolean $favorite
     * @return DataResponse
     */
    public function favorite($id, $favorite) {
        return $this->respond(function () use ($id, $favorite) {
            return $this->songsService->favorite($id, $favorite, $this->userId);
        });
    }


    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @return DataResponse
     */
    public function destroy($id) {
        return $this->respond(function () use ($id) {
            $this->songsService->delete($id, $this->userId);
            return [];
        });
    }

}
