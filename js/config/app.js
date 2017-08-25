/**
 * Copyright (c) 2013, Bernhard Posselt <dev@bernhard-posselt.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING file.
 */

/* jshint unused: false */
var app = angular.module('OpenLP', ['restangular', 'ngRoute']).
config(function($provide, $routeProvider, RestangularProvider, $httpProvider,
                $windowProvider) {
    'use strict';

    // Always send the CSRF token by default
    $httpProvider.defaults.headers.common.requesttoken = requestToken;

    // you have to use $provide inside the config method to provide a globally
    // shared and injectable object
    $provide.value('Constants', {
        saveInterval: 5*1000  // miliseconds
    });

    // define your routes that that load templates into the ng-view
    $routeProvider.when('/songs/:songId', {
        templateUrl: 'song.html',
        controller: 'SongController',
        resolve: {
            // $routeParams does not work inside resolve so use $route
            // song is the name of the argument that will be injected into the
            // controller
            /* @ngInject */
            song: function ($route, $q, is, Restangular) {

                var deferred = $q.defer();
                var songId = $route.current.params.songId;
                is.loading = true;

                Restangular.one('songs', songId).get().then(function (song) {
                    is.loading = false;
                    deferred.resolve(song);
                }, function () {
                    is.loading = false;
                    deferred.reject();
                });

                return deferred.promise;
            }
        }
    }).otherwise({
        redirectTo: '/'
    });

    var baseUrl = OC.generateUrl('/apps/openlp');
    RestangularProvider.setBaseUrl(baseUrl);



}).run(function ($rootScope, $location, SongsModel) {
    'use strict';

    $('link[rel="shortcut icon"]').attr(
		    'href',
		    OC.filePath('songs', 'img', 'favicon.png')
    );

    // handle route errors
    $rootScope.$on('$routeChangeError', function () {
        var songs = SongsModel.getAll();

        // route change error should redirect to the latest song if possible
        if (songs.length > 0) {
            var sorted = songs.sort(function (a, b) {
                if(a.modified > b.modified) {
                    return 1;
                } else if(a.modified < b.modified) {
                    return -1;
                } else {
                    return 0;
                }
            });

            var song = songs[sorted.length-1];
            $location.path('/songs/' + song.id);
        } else {
            $location.path('/');
        }
    });
});
