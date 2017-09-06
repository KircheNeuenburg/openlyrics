<?php
/**
 * Copyright (c) 2013, Bernhard Posselt <dev@bernhard-posselt.com>
 * Copyright (c) 2013, Jan-Christoph Borchardt http://jancborchardt.net
 * This file is licensed under the Affero General Public License version 3 or later.
 * See the COPYING file.
 */


script('openlp', [
    'vendor/bootstrap/tooltip',
    'vendor/angular/angular',
    'vendor/angular-route/angular-route',
    'vendor/restangular/dist/restangular',
    'vendor/underscore/underscore',
    'vendor/simplemde/dist/simplemde.min',
    'public/app.min'

]);

style('openlp', [
    '../js/vendor/simplemde/dist/simplemde.min',
    'vendor/bootstrap/tooltip',
    'openlp'
]);

?>

<div id="app" ng-app="OpenLP" ng-controller="AppController"
    ng-init="init(<?php p($_['lastViewedSong']); ?>)" ng-cloak>

    <script type="text/ng-template" id="song.html">
        <?php print_unescaped($this->inc('song')); ?>
    </script>

    <div id="app-navigation" ng-controller="SongsController">
        <ul>
            <li class="song-search">
                <span class="nav-entry icon-search">
                    <input type="text" ng-model="search" />
                </span>
            </li>
            <!-- new song button -->
            <div id="song-add">            
                <button class="icon-add app-content-list-button ng-binding" id="new-song-button" type="button" name="button" ng-click="create()"
                oc-click-focus="{ selector: '#app-content textarea' }">
                    <?php p($l->t('New song')); ?> 
                </button>
            </div>
            <!-- songs list -->
            <li ng-repeat="song in filteredSongs = (songs| and:search | orderBy:['-favorite','-modified'])"
                ng-class="{ active: song.id == route.songId }">
                <a href="#/songs/{{ song.id }}">
                    {{ song.title | songTitle }}
                </a>
                <span class="utils">
                    <button class="svg action icon-delete"
                        title="<?php p($l->t('Delete song')); ?>"
                        songs-tooltip
                        data-placement="bottom"
                        ng-click="delete(song.id)"></button>
                    <!--<button class="svg action icon-star"
                        title="<?php p($l->t('Favorite')); ?>"
                        songs-tooltip
                        data-placement="bottom"
                        ng-click="toggleFavorite(song.id)"
                        ng-class="{'icon-starred': song.favorite}"></button>-->
                </span>
            </li>
            <li ng-hide="filteredSongs.length">
                <span class="nav-entry">
                    <?php p($l->t('No songs found')); ?>
                </span>
            </li>

        </ul>
    </div>

    <div id="app-content" ng-class="{loading: is.loading}">
        <div id="app-content-container" ng-view></div>
    </div>
</div>
