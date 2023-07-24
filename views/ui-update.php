<?php

use romanzaytsev\cms\models\Languages;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$langs = Languages::getAll();

$classArr = explode('\\', get_class($model));
$className = array_pop( $classArr );

?>


<h1><?= is_null($model->id) ? "Новое" : "Редактировать" ?></h1>

<div class="tab" style="<?= true || is_null($model->id) || @$_GET['hideActions'] ? "display:none;" : "" ?>">
  <button class="tablinks" data-id="tab_content" onclick="openTab(event)">Контент</button>
  <button class="tablinks" data-id="tab_metadata" onclick="openTab(event)">Метаданные</button>
</div>

<?php $form = ActiveForm::begin(['id' => 'news-form', 'enableClientValidation' => false]); ?>

<div class="has-error">
	<div class="help-block">
		<?= $form->errorSummary($model); ?>
	</div>
</div>

<div class="tabcontent" id="tab_content">

<?= $form->field($model, 'parentId')->label(true)->textInput(['style'=>'']); ?>
<?= $form->field($model, 'id')->label(true)->textInput(['style'=>'', 'disabled' => ($model->isNewRecord ? false : 'disabled')]); ?>

<div style="">
	<label class="" for="album-name">Название</label>
	<?php
	echo "<span class='langbutton-container'>";
		foreach (array_keys($langs) as $i=>$lang):
		echo "<button type='button' onClick=\"changeLang(this, '".$lang."')\" class='langbutton ".($i>0?"":"active")."'>".$lang."</button>";
		endforeach;
	echo "</span>";
	foreach (array_keys($langs) as $i=>$lang):
		echo "<div class='langbox langbox-".$lang."' style='".($i>0?"height:0":"").";'><div>";
		echo $form->field($model, 'value'.$lang)->label(false)->textArea(['style'=>'height:60px;']);
		echo "</div></div>";
	endforeach;
	?>
</div>

<?php echo $form->field($model, 'href')->label(true)->textInput([]);  ?> <!-- href -->

    <?php echo $form->field($model, 'sort')->label(true)->textInput([]);  ?> <!-- Сортировка -->

    <?php echo $form->field($model, 'hide')->label(true)->checkbox([]);  ?>


<?php echo Html::hiddenInput('backPage', @$_REQUEST['backPage'], []);  ?>

<div id="albumBlocks">

</div>
</div>
<div class="tabcontent" id="tab_metadata">
	Test
</div>

		<br/>
    <?= Html::submitButton( (is_null($model->id) ? "Создать" : 'Сохранить'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'save-button']) ?>

<?php ActiveForm::end(); ?>

<?php if( empty( @$_REQUEST['backPage'] ) ): ?>
<script>
	window.addEventListener('DOMContentLoaded', (event) => {
    window.addEventListener('unload', function(event) {
	 	   if (window.parent == window.top) {
	 	      window.parent.$.magnificPopup.close();
	 	   }
    });
	});
</script>
<?php endif; ?>
