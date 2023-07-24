<?php
if ($returnAttributes ?? false) {
    return [
        'id' => 'main_page',
        'nameRu' => 'Главная',
        'nameEn' => 'Main',
        'view' => '/examplePageTemplates/main_page',
        'layout' => 'main',
        'type' => 'single',
        'blocks' => [
            [
                'id' => 'main_banner',
                'type' => 'Multimedia',
                'label' => 'Главный баннер',
                'properties' => [
                    'action' => 'item',
                    'src' => [
                        'parameters' => 'parameters[hideDescription]=1'
                    ],
                    'lang' => 'Ru',
                ]
            ],
            [
                'id' => 'content',
                'type' => 'PageBlockText',
                'label' => 'HTML content',
                'properties' => [
                    'type' => 'wysiwyg',
                    'lang' => 'Ru'
                ]
            ],
            [
                'id' => 'development_icons',
                'type' => 'Multimedia',
                'label' => 'Разработки',
                'properties' => [
                    'action' => 'list',
                    'src' => [
                        'parameters' => 'parameters[hideDescription]=1'
                    ],
                    'lang' => 'Ru'
                ]
            ],
        ],
    ];
}
?>
<div class="region content">
    <div class="wrap">
        <div class="block">
            <h1 class="h2"><?= $model->getName() ?></h1>
            <?= $model->getBlockValue('content') ?>
        </div>
    </div>
</div>
