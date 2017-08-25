/**
 * Copyright (c) 2013, Bernhard Posselt <dev@bernhard-posselt.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING file.
 */

describe('NoteController', function() {
    'use strict';

    var controller,
        scope,
        model,
        routeParams,
        song,
        SaveQueue;

    // use the Songs container
    beforeEach(module('Songs'));


    beforeEach(inject(function ($controller, $rootScope, SongsModel) {
        scope = $rootScope.$new();
        routeParams = {
            songId: 3
        };
        song = {
            id: 3,
            title: 'yo',
            content: 'hi im here\nand this is a line'
        };
        model = SongsModel;

        SaveQueue = {
            add: jasmine.createSpy('add')
        };

        controller = $controller('SongController', {
            $routeParams: routeParams,
            $scope: scope,
            SongsModel: model,
            SaveQueue: SaveQueue,
            song: song
        });

    }));


    it('should bind the correct song on scope', function () {
        expect(scope.song.title).toBe(song.title);
    });


    it ('should set the first line as title on save', function() {
        scope.updateTitle();
        expect(song.title).toBe('hi im here');
    });


    it ('should add the saved song to the save queue', function() {
        scope.save();
        expect(SaveQueue.add).toHaveBeenCalledWith(scope.song);
    });


    it ('should use new song if content is empty', function() {
        scope.song.content = '';
        scope.updateTitle();
        expect(song.title).toBe('New song');
    });

});