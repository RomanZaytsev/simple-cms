<?php
use yii\grid\GridView;
use yii\helpers\Html;
?>


  <h1>Альбом<?= ($model) ? ( ": ".@$model->getName() ) : " "?></h1>

<?= yii\helpers\Html::a(
    "<i class='icon-plus icon-white'></i>Добавить",
    yii\helpers\Url::to(['item',
        'id' => '',
        'albumId' => @$_GET['albumId'],
        'pageId' => @$_GET['pageId'],
        'blockId' => @$_GET['blockId'],
        'isPopup' => @$_REQUEST['isPopup'],
        'parameters' => @$_REQUEST['parameters'],
        'backPage' => @$_SERVER['REQUEST_URI'],
        'table' => @$_REQUEST['table'],
      ]),
    [ 'title' => 'Перейти', 'target' => '', 'class' => 'btn btn-info' ]
) ?>
<br/><br/>
<?php
echo GridView::widget([
  'dataProvider'=>$dataProvider,
  'columns'=>array(
    ['attribute'  =>  'nameRu'],
    ['attribute'  =>  'descriptionRu'],
    [
      'class' => 'yii\grid\ActionColumn',
      'header' => 'Actions',
      'headerOptions' => ['style' => 'color:#337ab7'],
      'template' => '{view} {item} {delete}',
      'contentOptions'=>['style'=>'width: 70px; pointer-events:none;'],
      'buttons' => [
        'view' => function ($url, $model_item) {
            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                        'title' => Yii::t('app', 'lead-view'), 'onClick'=>'event.stopPropagation();',
            ]);
        },
        'item' => function ($url, $model_item) {
            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                        'title' => Yii::t('app', 'lead-item'), 'onClick'=>'event.stopPropagation();',
            ]);
        },
        'delete' => function ($url, $model_item) {
            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                        'title' => Yii::t('app', 'lead-delete'), 'onClick'=>'event.stopPropagation();',
            ]);
        }

      ],
      'urlCreator' => function ($action, $model_item, $key, $index) {
        if ($action === 'view') {
            $url = $model_item->link;
            return $url;
        }
        if ($action === 'item') {
            $url = yii\helpers\Url::to(['item',
                'id'=>$model_item->id,
                'isPopup'=>@$_REQUEST['isPopup'],
                'backPage' => $_SERVER['REQUEST_URI'],
                'parameters' => @$_REQUEST['parameters'],
                'table' => @$_REQUEST['table'],
              ]);
            return $url;
        }
        if ($action === 'delete') {
            $url = yii\helpers\Url::to(['delete', 'table' => @$_REQUEST['table'], 'id'=>$model_item->id ]);
            return $url;
        }
      }
    ],
  ),
  'rowOptions' => function ($model, $key, $index, $grid) {
    return ['id' => $model['id'], 'onclick' => "$(this).find('[title=lead-item]').get(0).click()"];
  },
]); ?>
