<?php
use yii\grid\GridView;
use yii\helpers\Html;
?>

<h1>Элементы интерфейса</h1>
<style>
.table-striped > tbody > tr {
  cursor: pointer;
}
.table-striped > tbody > tr:hover {
  background-color: #e3f3f2;
}
.container {
    width: unset;
}
</style>

<?= yii\helpers\Html::a(
    "<i class='icon-plus icon-white'></i>Добавить",
    yii\helpers\Url::to(['ui-update',
        'pageTemplateId' => @$_GET['Page']['pageTemplateId'],
        'isPopup' => @$_REQUEST['isPopup'],
        'backPage' => @$_SERVER['REQUEST_URI'],
  ]),
    [ 'title' => 'Перейти', 'target' => '', 'class' => 'btn btn-info' ]
) ?>

<br/><br/>
<?php
echo GridView::widget([
  'dataProvider'=>$dataProvider,
  'filterModel' => $searchModel,
  'columns' => [
    ['attribute'  =>  'parentId'],
    ['attribute'  =>  'id'],
    ['attribute'  =>  'valueRu'],
    ['attribute'  =>  'valueEn'],
    ['attribute'  =>  'href'],
    ['attribute'  =>  'sort'],
    [
      'class' => 'yii\grid\ActionColumn',
      'header' => 'Actions',
      'template' => '{update} {delete}',
      'headerOptions' => [ 'color:#337ab7'],
      'contentOptions'=>[ 'width: 70px; pointer-events:none;'],
      'buttons' => [
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
        if ($action === 'update') {
            $url = yii\helpers\Url::to(['ui-update',
                    'id'=>$model->id,
                    'isPopup' => @$_REQUEST['isPopup'],
                    'backPage' => @$_SERVER['REQUEST_URI'],
                  ]);
            return $url;
        }
        if ($action === 'delete') {
            $url = yii\helpers\Url::to(['ui-delete', 'id'=>$model->id ]);
            return $url;
        }
      }
    ],
  ],
  'rowOptions' => function ($model, $key, $index, $grid) {
    return ['id' => $model['id'], 'onclick' => "$(this).find('[title=lead-update]').get(0).click()"];
  },
]); ?>
