/**
 * Copyright (c) 2013, Bernhard Posselt <dev@bernhard-posselt.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING file.
 */

app.directive('songsIsSaving', function ($window) {
    'use strict';
    return {
        restrict: 'A',
        scope: {
            'songsIsSaving': '='
        },
        link: function (scope) {
            $window.onbeforeunload = function () {
                if (scope.songsIsSaving) {
                    return t('songs', 'Note is currently saving. Leaving ' +
                                      'the page will delete all changes!');
                } else {
                    return null;
                }
            };
        }
    };
});
