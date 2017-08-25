/**
 * removes whitespaces and leading #
 */
app.filter('songTitle', function () {
	'use strict';
	return function (value) {
        	value = value.split('\n')[0] || 'newSong';
		return value.trim().replace(/^#+/g, '');
	};
});
