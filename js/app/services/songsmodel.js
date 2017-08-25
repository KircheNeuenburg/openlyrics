/**
 * Copyright (c) 2013, Bernhard Posselt <dev@bernhard-posselt.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING file.
 */

// take care of fileconflicts by appending a number
app.factory('SongsModel', function () {
    'use strict';

    var SongsModel = function () {
        this.songs = [];
        this.songsIds = {};
    };

    SongsModel.prototype = {
        addAll: function (songs) {
            for(var i=0; i<songs.length; i+=1) {
                this.add(songs[i]);
            }
        },
        add: function(song) {
            this.updateIfExists(song);
        },
        getAll: function () {
            return this.songs;
        },
        get: function (id) {
            return this.songsIds[id];
        },
        updateIfExists: function(updated) {
            var song = this.songsIds[updated.id];
            if(angular.isDefined(song)) {
                song.title = updated.title;
                song.modified = updated.modified;
                song.content = updated.content;
                song.favorite = updated.favorite;
            } else {
                this.songs.push(updated);
                this.songsIds[updated.id] = updated;
            }
        },
        remove: function (id) {
            for(var i=0; i<this.songs.length; i+=1) {
                var song = this.songs[i];
                if(song.id === id) {
                    this.songs.splice(i, 1);
                    delete this.songsIds[id];
                    break;
                }
            }
        }
    };

    return new SongsModel();
});
