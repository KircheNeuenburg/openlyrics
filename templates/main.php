<?php
/**
 * Copyright (c) 2013, Bernhard Posselt <dev@bernhard-posselt.com>
 * Copyright (c) 2013, Jan-Christoph Borchardt http://jancborchardt.net
 * This file is licensed under the Affero General Public License version 3 or later.
 * See the COPYING file.
 */


script('openlp', [
    '../build/build'

]);

style('openlp', [
    'openlp'
]);

?>

<div id="app">
	<nc-app-navigation></nc-app-navigation>
	<nc-app-content></nc-app-content>
</div>
