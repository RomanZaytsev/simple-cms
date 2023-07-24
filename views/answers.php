<?php
use yii\grid\GridView;
use yii\helpers\Html;
?>

<h1>Анкеты</h1>

<?php
echo GridView::widget([
  'dataProvider'=>$dataProvider,
  'columns'=>array(
    ['attribute'  =>  'formId'],
    ['attribute'  =>  'value'],
    ['attribute'  =>  'datePublications'],
    [
      'class' => 'yii\grid\ActionColumn',
      'header' => 'Actions',
      'template' => '{view}',
      'headerOptions' => [ 'color:#337ab7'],
      'contentOptions'=>[ 'width: 70px; pointer-events:none;'],
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
            $url = yii\helpers\Url::to([ 'answer',
              "sessionId"=>$model->sessionId,
              'backPage' => @$_SERVER['REQUEST_URI'],
            ]);
            return $url;
        }
        if ($action === 'update') {
            $url = yii\helpers\Url::to([ 'updateAnswer' ]);
            return $url;
        }
        if ($action === 'delete') {
            $url = yii\helpers\Url::to([ 'deleteAnswer' ]);
            return $url;
        }
      }
    ],
  ),
  'rowOptions' => function ($model, $key, $index, $grid) {
    return ['sessionId' => $model['sessionId'], 'onclick' => "$(this).find('[title=lead-view]').get(0).click()"];
  },
]); ?>
