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

return ['routes' => [
    // page
    ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],

    // songs
    ['name' => 'songs#index', 'url' => '/songs', 'verb' => 'GET'],
    ['name' => 'songs#get', 'url' => '/songs/{id}', 'verb' => 'GET'],
    ['name' => 'songs#create', 'url' => '/songs', 'verb' => 'POST'],
    ['name' => 'songs#update', 'url' => '/songs/{id}', 'verb' => 'PUT'],
    ['name' => 'songs#favorite', 'url' => '/songs/{id}/favorite', 'verb' => 'PUT'],
    ['name' => 'songs#destroy', 'url' => '/songs/{id}', 'verb' => 'DELETE'],

    // api
    ['name' => 'songs_api#index', 'url' => '/api/v0.2/songs', 'verb' => 'GET'],
    ['name' => 'songs_api#get', 'url' => '/api/v0.2/songs/{id}', 'verb' => 'GET'],
    ['name' => 'songs_api#create', 'url' => '/api/v0.2/songs', 'verb' => 'POST'],
    ['name' => 'songs_api#update', 'url' => '/api/v0.2/songs/{id}', 'verb' => 'PUT'],
    ['name' => 'songs_api#destroy', 'url' => '/api/v0.2/songs/{id}', 'verb' => 'DELETE'],
    ['name' => 'songs_api#preflighted_cors', 'url' => '/api/v0.2/{path}',
     'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']],
]];
