/**
 * Copyright (c) 2013, Bernhard Posselt <dev@bernhard-posselt.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING file.
 */

// This is available by using ng-controller="SongsController" in your HTML
app.controller('SongsController', function($routeParams, $scope, $location,
                                           Restangular, SongsModel) {
    'use strict';

    $scope.route = $routeParams;
    $scope.songs = SongsModel.getAll();

    var songsResource = Restangular.all('songs');

    // initial request for getting all songs
    songsResource.getList().then(function (songs) {
        SongsModel.addAll(songs);
    });

    $scope.create = function () {
        songsResource.post().then(function (song) {
            SongsModel.add(song);
            $location.path('/songs/' + song.id);
        });
    };

    $scope.delete = function (songId) {
        var song = SongsModel.get(songId);
        song.remove().then(function () {
            SongsModel.remove(songId);
            $scope.$emit('$routeChangeError');
        });
    };

    $scope.toggleFavorite = function (songId) {
        var song = SongsModel.get(songId);
        song.customPUT({favorite: !song.favorite},
            'favorite', {}, {}).then(function (favorite) {
            song.favorite = favorite ? true : false;
        });
    };

});
