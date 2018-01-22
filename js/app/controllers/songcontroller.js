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
        $scope.song.title ||
            t('songs', 'New song');
    };

    $scope.save = debounce(function() {
        var song = $scope.song;
        //insert transform here
        SaveQueue.add(song);
    }, 300);

    

});
