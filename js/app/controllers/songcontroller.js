/**
 * Copyright (c) 2013, Bernhard Posselt <dev@bernhard-posselt.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING file.
 */

app.controller('SongController', function($routeParams, $scope, SongsModel,
                                          SaveQueue, song, debounce) {
    'use strict';

    SongsModel.updateIfExists(song);

    $scope.song = SongsModel.get($routeParams.songId);

    $scope.isSaving = function () {
        return SaveQueue.isSaving();
    };

    $scope.updateTitle = function () {
        $scope.song.title = $scope.song.content.split('\n')[0] ||
            t('songs', 'New song');
    };

    $scope.save = debounce(function() {
        var song = $scope.song;
        SaveQueue.add(song);
    }, 300);

    $scope.toggleDistractionFree = function() {
        function launchIntoFullscreen(element) {
            if(element.requestFullscreen) {
                element.requestFullscreen();
            } else if(element.mozRequestFullScreen) {
                element.mozRequestFullScreen();
            } else if(element.webkitRequestFullscreen) {
                element.webkitRequestFullscreen();
            } else if(element.msRequestFullscreen) {
                element.msRequestFullscreen();
            }
        }

        function exitFullscreen() {
            if(document.exitFullscreen) {
                document.exitFullscreen();
            } else if(document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if(document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        }

        if(document.fullscreenElement ||
           document.mozFullScreenElement ||
           document.webkitFullscreenElement) {
            exitFullscreen();
        } else {
            launchIntoFullscreen(document.getElementById('app-content'));
        }
    };

});
