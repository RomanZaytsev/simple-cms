<?php
if ($returnAttributes ?? false) {
    return [
        'id' => 'simple-content',
        'nameRu' => 'Простая страница',
        'nameEn' => 'Simple Content',
        'view' => '/examplePageTemplates/simple-content',
        'layout' => 'main',
        'type' => 'multiple',
        'blocks' => [
            [
                'id' => 'content',
                'type' => 'PageBlockText',
                'label' => 'HTML content',
                'properties' => [
                    'type' => 'wysiwyg',
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
