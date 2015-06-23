<?php

use BoomCMS\Core\Page;

return [
    'viewHelpers' => [
        'assetURL' => function(array $params) {
            if ( !isset($params['action'])) {
                $params['action'] = 'view';
            }

            if (isset($params['height']) && !isset($params['width'])) {
                $params['width'] = 0;
            }

            return route('asset', $params);
        },
        'getPages' => function(array $params) {
            return (new Page\Query($params))->getPages();
        },
        'next' => function(array $params = []) {
            return (new Page\Query($params))->getNextTo(Editor::getActivePage(), 'after');
        },
        'prev' => function(array $params = []) {
            return (new Page\Query($params))->getNextTo(Editor::getActivePage(), 'before');
        },
    ],
];