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

    var x2js = new X2JS();
    
    
    
    $scope.songjson = x2js.xml_str2json( $scope.song.content);
    var parser = new DOMParser();
    var songxml = parser.parseFromString($scope.song.content,"text/xml");
    var names = [];
    var n = songxml.getElementsByTagName("title");
    if (n[0]) {
      for (var i = 0, len1 = n.length; i < len1; i++) {
        names.push({"title": n[i].firstChild.nodeValue, 
            "lang": n[i].getAttribute("lang")});
      }
    }
    $scope.songjson.song.properties.titles.title = names[0].title;

    $scope.isSaving = function () {
        return SaveQueue.isSaving();
    };

    $scope.updateTitle = function () {
        $scope.song.title = $scope.songjson.song.properties.titles.title ||
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
