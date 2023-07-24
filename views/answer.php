<?php
use romanzaytsev\cms\models\Languages;
use yii\grid\GridView;

$langs = Languages::getAll();

?>


<h1>Ответы на анкету</h1>

<?php
echo GridView::widget([
  'dataProvider'=>$dataProvider,
  'columns'=>array(
    ['attribute'  =>  'fieldId'],
    ['attribute'  =>  'value']
  ),
]); ?>
