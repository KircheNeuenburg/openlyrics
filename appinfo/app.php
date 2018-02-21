<?php
/**
 * Nextcloud - Notes
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Bernhard Posselt <dev@bernhard-posselt.com>
 * @copyright Bernhard Posselt 2012, 2014
 */

namespace OCA\OpenLyrics\AppInfo;

use OCP\AppFramework\App;

$app = new App('openlyrics');
$container = $app->getContainer();

$container->query('OCP\INavigationManager')->add(function () use ($container) {
    $urlGenerator = $container->query('OCP\IURLGenerator');
    $l10n = $container->query('OCP\IL10N');
    return [
        'id' => 'openlyrics',
        'order' => 10,
        'href' => $urlGenerator->linkToRoute('openlyrics.page.index'),
        'icon' => $urlGenerator->imagePath('openlyrics', 'openlyrics.svg'),
        'name' => $l10n->t('OpenLyrics')
    ];
});

