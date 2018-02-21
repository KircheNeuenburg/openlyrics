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

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\OpenLyrics\Service\SongDoesNotExistException;

/**
 * Class Errors
 *
 * @package OCA\OpenLyrics\Controller
 */
trait Errors {
    /**
     * @param $callback
     * @return DataResponse
     */
    protected function respond ($callback) {
        try {
            return new DataResponse($callback());
        } catch(SongDoesNotExistException $ex) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
    }
}
