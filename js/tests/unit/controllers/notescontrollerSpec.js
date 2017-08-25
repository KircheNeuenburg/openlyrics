/**
 * Copyright (c) 2013, Bernhard Posselt <dev@bernhard-posselt.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING file.
 */

describe('SongsController', function() {
    'use strict';

    var controller,
        scope,
        model,
        routeParams,
        location,
        http;

    // use the Song container
    beforeEach(module('Songs'));

    beforeEach(inject(function ($controller, $rootScope, $httpBackend,
        SongsModel) {
        http = $httpBackend;
        scope = $rootScope.$new();
        routeParams = {
            songId: 3
        };
        model = SongsModel;
        location = {
            path: jasmine.createSpy('path')
        };
        controller = $controller;
    }));


    it ('should load songs and attach them to scope', function() {
        var songs = [
            {id: 3, title: 'hey'}
        ];
        http.expectGET('/songs').respond(200, songs);

        controller = controller('SongsController', {
            $routeParams: routeParams,
            $scope: scope,
            $location: location,
            SongsModel: model
        });

        http.flush(1);

        expect(scope.songs[0].title).toBe('hey');
        expect(scope.route).toBe(routeParams);
    });


    it ('should do a create request', function() {
        http.expectGET('/songs').respond(200, [{}]);

        controller = controller('SongsController', {
            $routeParams: routeParams,
            $scope: scope,
            $location: location,
            SongsModel: model
        });

        http.flush(1);

        var song = {
            id: 3,
            title: 'yo'
        };
        http.expectPOST('/songs').respond(song);
        scope.create();
        http.flush(1);

        expect(model.get(3).title).toBe('yo');
        expect(location.path).toHaveBeenCalledWith('/songs/3');
    });


    it ('should delete a song', function () {
        var songs = [
            {id: 3, title: 'hey'}
        ];

        http.expectGET('/songs').respond(200, songs);

        controller = controller('SongsController', {
            $routeParams: routeParams,
            $scope: scope,
            $location: location,
            SongsModel: model
        });

        http.flush(1);

        http.expectDELETE('/songs/3').respond(200, {});
        scope.$emit = jasmine.createSpy('$emit');
        scope.delete(3);
        http.flush(1);

        expect(model.get(3)).not.toBeDefined();
        expect(scope.$emit).toHaveBeenCalledWith('$routeChangeError');
    });


    afterEach(function() {
        http.verifyNoOutstandingExpectation();
        http.verifyNoOutstandingRequest();
    });


});







