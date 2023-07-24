<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<style>
table td {
  vertical-align: top;
}
#listBox {
  width: 200px;
}
.dataContainer select {
  overflow: auto;
}
</style>

<div>
	<div class="page-header"><h4><?= Html::encode($this->title) ?></h4></div>
	<span class="btn btn-primary addElement" style="">Добавить элемент</span>
	<div class="clearfix"></div>
	<table>
		<tr>
			<td class="dataContainer">
        <select multiple id="listBox" size="25">
          <?php foreach ($list as $model): ?>
            <option value="<?= $model->id ?>"><?= $model->id ?></option>
          <?php endforeach; ?>
        </select>
			</td>
			<td style="vertical-align:top;padding:0px 20px" class="editContainer">

			</td>
		</tr>
	</table>
</div>
<script>
  var table = '<?=$table?>';
  var id = '';
  function listbox_change() {
    if(id == '') {
      $('.editContainer').html('');
      return;
    }
    $('.editContainer').html('loading....');
    $.ajax({
      url: "<?= $this->context->URLto(["config/edit"]) ?>",
      async: true,
      data: ({
        table: table,
        id: id
      }),
      dataType: 'html',
      type: "GET",
      success: function(response) {
        $('.editContainer').html(response);
        setEventHandler();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        $('.editContainer').html('Ошибка: ' + textStatus);
      },
    });
  }
	window.addEventListener("load", function() {

		$(".addElement").click(function() {
			if(table) {
				$.ajax({
					url: "<?= $this->context->URLto(["config/edit"]) ?>",
					async: false,
					data: ({
						table: table,
						'new':1
					}),
					dataType: 'html',
					type: "GET",
					success: function(response) {
						$('.editContainer').html(response);
            setEventHandler();
					},
					error: function(jqXHR, textStatus, errorThrown) {
						alert('Ошибка: ' + textStatus);
					},
				});
			}
		});

    $("#listBox").on('click', function() {
      id = this.value.trim();
      listbox_change();
    });
		$("#listBox").on('change', function() {
			id = this.value.trim();
      listbox_change();
		});

		$("#goDecline").on('click', function() {
			$('.editContainer').html('');
		});

		$("#goDelete").on('click', function() { deleteData(false); });
	});
</script>
