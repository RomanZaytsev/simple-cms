<?php
use yii\grid\GridView;
use yii\helpers\Html;
?>

<h1>Альбомы</h1>
<?= yii\helpers\Html::a(
    "<i class='icon-plus icon-white'></i>Добавить",
    yii\helpers\Url::to(['album',
      'isPopup' => @$_REQUEST['isPopup'],
      'backPage' => @$_SERVER['REQUEST_URI'],
    ]),
    [ 'title' => 'Перейти', 'target' => '', 'class' => 'btn btn-info' ]
) ?>
<br/><br/>
<?php
echo GridView::widget([
  'dataProvider'=>$dataProvider,
  'columns'=>array(
    ['attribute'  =>  'nameRu'],
    [
      'class' => 'yii\grid\ActionColumn',
      'header' => 'Actions',
      'headerOptions' => ['style' => 'color:#337ab7'],
      'template' => '{view} {update} {delete}',
      'contentOptions'=>['style'=>'width: 70px; pointer-events:none;'],
      'buttons' => [
        'view' => function ($url, $model) {
            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                        'title' => Yii::t('app', 'lead-view'), 'onClick'=>'event.stopPropagation();',
            ]);
        },
        'update' => function ($url, $model) {
            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                        'title' => Yii::t('app', 'lead-update'), 'onClick'=>'event.stopPropagation();',
            ]);
        },
        'delete' => function ($url, $model) {
            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                        'title' => Yii::t('app', 'lead-delete'), 'onClick'=>'event.stopPropagation();',
            ]);
        }

      ],
      'urlCreator' => function ($action, $model, $key, $index) {
        if ($action === 'view') {
            $url = yii\helpers\Url::to(['list',
              'albumId' => $model->id,
              'isPopup' => @$_REQUEST['isPopup'],
              'backPage' => $_SERVER['REQUEST_URI'],
            ]);
            return $url;
        }
        if ($action === 'update') {
            $url = yii\helpers\Url::to(['album', 'id'=>$model->id, 'isPopup'=>@$_REQUEST['isPopup'] ]);
            return $url;
        }
        if ($action === 'delete') {
            $url = yii\helpers\Url::to(['deletealbum', 'id'=>$model->id ]);
            return $url;
        }
      }
    ],
  ),
  'rowOptions' => function ($model, $key, $index, $grid) {
    return ['id' => $model['id'], 'onclick' => "$(this).find('[title=lead-view]').get(0).click()"];
  },
]);
?>
