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

    // notes
    ['name' => 'openlp#index', 'url' => '/openlp', 'verb' => 'GET'],
    ['name' => 'openlp#get', 'url' => '/openlp/{id}', 'verb' => 'GET'],
    ['name' => 'openlp#create', 'url' => '/openlp', 'verb' => 'POST'],
    ['name' => 'openlp#update', 'url' => '/openlp/{id}', 'verb' => 'PUT'],
    ['name' => 'openlp#favorite', 'url' => '/openlp/{id}/favorite', 'verb' => 'PUT'],
    ['name' => 'openlp#destroy', 'url' => '/openlp/{id}', 'verb' => 'DELETE'],

    // api
    ['name' => 'notes_api#index', 'url' => '/api/v0.2/notes', 'verb' => 'GET'],
    ['name' => 'notes_api#get', 'url' => '/api/v0.2/notes/{id}', 'verb' => 'GET'],
    ['name' => 'notes_api#create', 'url' => '/api/v0.2/notes', 'verb' => 'POST'],
    ['name' => 'notes_api#update', 'url' => '/api/v0.2/notes/{id}', 'verb' => 'PUT'],
    ['name' => 'notes_api#destroy', 'url' => '/api/v0.2/notes/{id}', 'verb' => 'DELETE'],
    ['name' => 'notes_api#preflighted_cors', 'url' => '/api/v0.2/{path}',
     'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']],
]];
