<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<script>
function blockForm_success(e) {
		$.ajax({
			url: "",
			dataType: 'html',
			type: "GET",
			success: function(response) {
				$("#listBox").html( ($(response).find("#listBox").html()) );
				var formdata = $( e.target ).serializeArray();
				for(var key in formdata) {
					if(formdata[key].name == "Config[id]") {
						id = formdata[key].value;
						break;
					}
				}
				listbox_change();
				$('.editContainer').append('<div style="display:none;padding:5px;margin-bottom: 5px" class="alert-success">\n\
					<h4 style="padding:0px;margin: 0">Изменения сохранены</h4>\n\</div>');
				$(".alert-success").fadeIn(500, function(){$(this).fadeOut(3000,function(){this.remove();});});
			},
		});
}
function blockForm_error() {
	alert("ошибка");
}
function blockForm_complete() { }
function deleteData() {
	$.ajax({
		url: "<?= $this->context->URLto(["config/delete"]) ?>",
		async: true,
		data: ({
			table: table,
			id: id,
		}),
		dataType: 'json',
		type: "POST",
		beforeSend: function() {
			$(".row_btnset").hide();
			$(".row_loading").show();
			$(".alert-success").remove();
			$(".alert-error").remove();
			document.body.style.cursor = 'wait';
		},
		success: function(response) {
			if(response.used) {
				if (confirm(response.text)) {
					deleteData(true);
				} else {
					this.error(null,response.text);
				}
			} else
			if(response.success) {
				$('.editContainer').html('');
				$('.editContainer').append('<div style="display:none;padding:5px;margin-bottom: 5px" class="alert-success">\n\
					<h4 style="padding:0px;margin: 0">Запись удалена</h4>\n\</div>');
				$(".alert-success").fadeIn(500, function(){$(this).fadeOut(3000,function(){this.remove();});});
				blockForm_success();
			} else {
				this.error(null,response.text);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$('.editContainer').prepend('<div style="display:none;padding:5px;margin-bottom: 5px" class="alert-error">\n\<h4 style="padding:0px;margin: 0">Изменения сохранены</h4>\n\</div>');
			$(".alert-error").text('Ошибка: ' + textStatus);
			$(".alert-error").fadeIn(500);
		},
		complete: function() {
			$(".row_btnset").show();
			$(".row_loading").hide();
			document.body.style.cursor = 'default';
			window.scrollTo(0,0);
		},
	});
};
</script>
<?php
$form = ActiveForm::begin(['id' => 'blockForm', 'options'=>['enctype' => 'multipart/form-data','data-event'=>'submit:ajaxFormSubmit,success:blockForm_success,error:blockForm_error,complete:blockForm_complete']]);
if(@$_GET['layout']=='simple') {
	$form = ActiveForm::begin(['id' => 'blockForm', 'options'=>['enctype' => 'multipart/form-data']]);
}
 ?>
<style>
	.blockData .inputArea {
		padding:5px;
	}
	.inputArea input[type="text"] {
		width:400px;
	}
	input:-moz-read-only {
    background-color: #eaeaea;
		cursor:default;
	}
	input:read-only {
	    background-color: #eaeaea;
			cursor:default;
	}
</style>
<table class="blockData">
	<?php if ($model->isNewRecord): ?>
  	<?= $form->field($model, 'id')->textInput([])->label(true) ?>
	<?php else: ?>
  	<?= $form->field($model, 'id')->textInput(['readonly'=>'readonly'])->label(true) ?>
	<?php endif; ?>
	<?php if (isset($model->value) || $model->hasProperty('value')): ?>
	  <?= $form->field($model, 'value')->textInput(['placeholder'=>'Значение', 'autofocus' => true])->label(true) ?>
	<?php endif; ?>
	<?php if ($model->hasProperty('priority')): ?>
	  <?= $form->field($model, 'priority')->textInput(['type' => 'number', 'autofocus' => true])->label(true) ?>
	<?php endif; ?>
	<?php
	foreach ($model->getValidators() as $validator) {
			if (isset($validator->maxFiles)) {
				foreach ($validator->attributes as $attr) {
					echo $form->field($model, $attr)->fileInput();
				}
			}
	};
	?>
	<?php foreach ($childsValue as $childname => $child): ?>
		<?php foreach ($child as $key => $value): ?>
		<div class="form-group field-ui-value">
			<label class="control-label" for="ui-value"><?=$key?></label>
			<br/>
	  	<textarea name="UI[<?=$childname?>][<?=$key?>]" style="width:800px; height:200px;"><?= $value ?></textarea>
		</div>
		<?php endforeach; ?>
	<?php endforeach; ?>
	<tr>
		<td>
			<br>
		</td>
	</tr>
	<tr class='row_btnset'>
		<td>
			<button class="btn btn-success">Сохранить</button>
			<button type="button" class="btn btn-warning" data-event="click:deleteData">Удалить</button>
		</td>
	</tr>
	<tr class='row_loading' style='display:none;'>
		<td>
			<br>
		</td>
		<td class="screen_loading">
			<br>
		</td>
	</tr>
</table>
<?php ActiveForm::end(); ?>

<script>
</script>
