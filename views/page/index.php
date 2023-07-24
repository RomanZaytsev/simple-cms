<?php

use yii\grid\GridView;
use yii\helpers\Html;

$category = @\romanzaytsev\cms\models\PageTemplate::getById(@$_GET['Page']['pageTemplateId']);
$parent = @\romanzaytsev\cms\models\Page::find()->where(['id' => @$_GET['Page']['parentPageId']])->one();
?>
<h1>
    <?= @$category ? @$category->nameRu : "Страницы" ?>
    <?= @$parent ? ": " . @$parent->nameRu : "" ?>
</h1>

<?= yii\helpers\Html::a(
    "<i class='icon-plus icon-white'></i>Добавить",
    yii\helpers\Url::to(['update',
        'pageTemplateId' => @$_GET['Page']['pageTemplateId'],
        'isPopup' => @$_REQUEST['isPopup'],
        'backPage' => @$_SERVER['REQUEST_URI'],
        'hideActions' => @$_GET['hideActions'],
    ]),
    ['title' => 'Перейти', 'target' => '', 'class' => 'btn btn-info']
) ?>
<br/>
<br/>

<?php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => @$_GET['hideActions'] ? null : $searchModel,
    'columns' => array(
        ['attribute' => 'nameRu'],
        ['attribute' => 'pageTemplateId'],
        ['attribute' => 'datePublications'],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'template' => '{view} {update} {delete}',
            'headerOptions' => ['style' => 'color: #337ab7;'],
            'contentOptions' => ['style' => 'width: 70px; pointer-events:none; text-align: center;'],
            'buttons' => [
                'view' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                        'title' => Yii::t('app', 'lead-view'),
                        'onClick' => 'event.stopPropagation();',
                        'target' => 'blank',
                    ]);
                },
                'update' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                        'title' => Yii::t('app', 'lead-update'),
                        'onClick' => 'event.stopPropagation();',
                    ]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                        'title' => Yii::t('app', 'lead-delete'),
                        'onClick' => 'event.stopPropagation(); return confirm("Are you absolutely sure?")',
                    ]);
                }

            ],
            'urlCreator' => function ($action, $model, $key, $index) {
                if ($action === 'view') {
                    $url = yii\helpers\Url::to($model->getUrl());
                    return $url;
                }
                if ($action === 'update') {
                    $url = yii\helpers\Url::to(['update',
                        'id' => $model->id,
                        'isPopup' => @$_REQUEST['isPopup'],
                        'backPage' => @$_SERVER['REQUEST_URI'],
                        'hideActions' => @$_GET['hideActions'],
                    ]);
                    return $url;
                }
                if ($action === 'delete') {
                    $url = yii\helpers\Url::to(['delete', 'id' => $model->id]);
                    return $url;
                }
            }
        ],
    ),
    'rowOptions' => function ($model, $key, $index, $grid) {
        return ['id' => $model['id'], 'onclick' => "$(this).find('[title=lead-update]').get(0).click()"];
    },
]); ?>
