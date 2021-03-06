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
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\IRequest;
use OCP\IConfig;

use OCA\OpenLyrics\Service\SongsService;
use OCA\OpenLyrics\Service\SongDoesNotExistException;

/**
 * Class PageController
 *
 * @package OCA\OpenLyrics\Controller
 */
class PageController extends Controller {

    /** @var SongsService */
    private $songsService;
    /** @var IConfig */
    private $settings;
    /** @var string */
    private $userId;

    /**
     * @param string $AppName
     * @param IRequest $request
     * @param SongsService $songsService
     * @param IConfig $settings
     * @param string $UserId
     */
    public function __construct($AppName, IRequest $request, $UserId,
                                SongsService $songsService, IConfig $settings){
        parent::__construct($AppName, $request);
        $this->songsService = $songsService;
        $this->userId = $UserId;
        $this->settings = $settings;
    }


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @return TemplateResponse
     */
    public function index() {
        $lastViewedSong = (int) $this->settings->getUserValue($this->userId,
            $this->appName, 'LastViewedSong');
        // check if song exists
        try {
            $this->songsService->get($lastViewedSong, $this->userId);
        } catch(SongDoesNotExistException $ex) {
            $lastViewedSong = 0;
        }

        $response = new TemplateResponse(
            $this->appName,
            'main',
            [
                'lastViewedSong' => $lastViewedSong
            ]
        );

        $csp = new ContentSecurityPolicy();
        $csp->addAllowedImageDomain('*');
        $response->setContentSecurityPolicy($csp);

        return $response;
    }


}