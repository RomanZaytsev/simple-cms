<?php

use romanzaytsev\cms\models\Languages;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$langs = Languages::getAll();

$classArr = explode('\\', get_class($model));
$className = array_pop($classArr);

?>


<h1><?= is_null($model->id) ? "Новое" : "Редактировать" ?></h1>

<div class="tab" style="<?= is_null(null) ? "display:none;" : "" ?>">
    <button class="tablinks" data-id="tab_content" onclick="openTab(event)">Контент</button>
</div>

<?php $form = ActiveForm::begin(['id' => 'news-form', 'enableClientValidation' => false]); ?>

<div class="has-error">
    <div class="help-block">
        <?= $form->errorSummary($model); ?>
    </div>
</div>

<div class="tabcontent" id="tab_content">


    <?php if (!@$_REQUEST['parameters']['hideMediafile']): ?>

        <div id="preview-container" style="display:block;">
            <iframe style="display:none;" width="560" height="315" src="" frameborder="0"
                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            <img style="display:none; max-width:400px;" src="">
        </div>
        <br/>

        <div class="form-group field-multimedia-link" style="display: inline-block; width: 100%; ">
            <label class="control-label" for="multimedia-link" style="display: block; ">Медиа-файл</label>
            <input type="text" id="multimedia-link" class="form-control" name="<?= $className ?>[link]"
                   value="<?= htmlentities($model->link) ?>" style="float: left; width: 80%; ">
            <button type="button" onclick="$('#upload_file').click()"
                    style="float: right;width: 19%;height: 34px;border: 0;border-radius: 5px;font-weight: 800;"
                    class="btn-primary">Выбрать файл
            </button>
            <input type="file" data-dir="multimedia/" class="upload_file" id="upload_file" multiple
                   style="display:none;"/>
        </div>

    <?php endif; ?>

    <?php if (@$_REQUEST['parameters']['showPreview']): ?>
        <div class="form-group field-multimedia-link" style="display: inline-block; width: 100%; ">
            <label class="control-label" for="multimedia-link" style="display: block; ">Preview-изображение</label>
            <input type="text" id="multimedia-link" class="form-control" name="<?= $className ?>[preview]"
                   value="<?= htmlentities($model->preview) ?>" style="float: left; width: 80%; ">
            <button type="button" onclick="$('#upload_preview').click()"
                    style="float: right;width: 19%;height: 34px;border: 0;border-radius: 5px;font-weight: 800;"
                    class="btn-primary">Выбрать файл
            </button>
            <input type="file" data-dir="multimedia/" class="upload_file" id="upload_preview" multiple
                   style="display:none;"/>
        </div>
    <?php endif; ?>

    <?php if (@$_REQUEST['parameters']['showHref']): ?>
        <?= $form->field($model, 'href') ?>
    <?php endif; ?>

    <?php if (!@$_REQUEST['parameters']['hideName']): ?>
        <div style="">
            <label class="" for="album-name">Название</label>
            <?php
            echo "<span class='langbutton-container'>";
            foreach (array_keys($langs) as $i => $lang):
                echo "<button type='button' onClick=\"changeLang(this, '" . $lang . "')\" class='langbutton " . ($i > 0 ? "" : "active") . "'>" . $lang . "</button>";
            endforeach;
            echo "</span>";
            foreach (array_keys($langs) as $i => $lang):
                echo "<div class='langbox langbox-" . $lang . "' style='" . ($i > 0 ? "height:0" : "") . ";'><div>";
                echo $form->field($model, 'name' . $lang)->label(false)->textArea(['style' => 'height:60px;']);
                echo "</div></div>";
            endforeach;
            ?>
        </div>
    <?php endif; ?>

    <?php if (!@$_REQUEST['parameters']['hideDescription']): ?>
        <div style="">
            <label class="" for="album-name">Описание</label>
            <?php
            echo "<span class='langbutton-container'>";
            foreach (array_keys($langs) as $i => $lang):
                echo "<button type='button' onClick=\"changeLang(this, '" . $lang . "')\" class='langbutton " . ($i > 0 ? "" : "active") . "'>" . $lang . "</button>";
            endforeach;
            echo "</span>";
            foreach (array_keys($langs) as $i => $lang):
                echo "<div class='langbox langbox-" . $lang . "' style='" . ($i > 0 ? "height:0" : "") . ";'><div>";
                echo $form->field($model, 'description' . $lang)->label(false)->textArea(['style' => 'height:240px;']);
                echo "</div></div>";
            endforeach;
            ?>
        </div>
    <?php endif; ?>
    <?php if (@$_REQUEST['parameters']['showFullText']): ?>
        <div style="">
            <label class="" for="album-name">Полнотекстовое описание</label>
            <?php
            echo "<span class='langbutton-container'>";
            foreach (array_keys($langs) as $i => $lang):
                echo "<button type='button' onClick=\"changeLang(this, '" . $lang . "')\" class='langbutton " . ($i > 0 ? "" : "active") . "'>" . $lang . "</button>";
            endforeach;
            echo "</span>";
            foreach (array_keys($langs) as $i => $lang):
                echo "<div class='langbox langbox-" . $lang . "' style='" . ($i > 0 ? "height:0" : "") . ";'><div>";
                echo $form->field($model, 'fullText' . $lang)->label(false)->widget(vova07\imperavi\Widget::classname(), [
                    'options' => [
                        'lang' => 'ru',
                        'imageUpload' => '/admin/file/upload',
                        'fileUpload' => '/admin/file/upload',
                        'minHeight' => "240px",
                        'replaceDivs' => false,
                    ],
                ]);
                echo "</div></div>";
            endforeach;
            ?>
        </div>
    <?php endif; ?>

    <?php
    if (@$_REQUEST['parameters']['showDate'])
        echo $form->field($model, 'date')->widget(kartik\datetime\DateTimePicker::classname(), [
            'options' => ['placeholder' => 'yyyy-mm-dd'],
            'type' => kartik\datetime\DateTimePicker::TYPE_INPUT,
            'pluginOptions' => [
                'autoclose' => true
            ]
        ]);
    ?>

    <?php if (@$_REQUEST['parameters']['showProperties']): ?>
        <?php echo $form->field($model, 'properties')->label(true)->textArea(['style' => 'height:120px;']); ?>
    <?php endif; ?>

    <?php if (@$_REQUEST['backPage']): ?>
        <?php echo $form->field($model, 'sort')->label(true)->textInput([]); ?>
    <?php endif; ?>

    <?php echo $form->field($model, 'albumId')->label(false)->hiddenInput([]); ?>
    <?php echo $form->field($model, 'pageId')->label(false)->hiddenInput([]); ?>
    <?php echo $form->field($model, 'blockId')->label(false)->hiddenInput([]); ?>

    <?php echo Html::hiddenInput('backPage', @$_REQUEST['backPage'], []); ?>

    <div id="albumBlocks">

    </div>
</div>
<div class="tabcontent" id="tab_metadata">
    Test
</div>

<br/>
<?= Html::submitButton((is_null($model->id) ? "Создать" : 'Сохранить'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'save-button']) ?>

<?php ActiveForm::end(); ?>

<?php if (empty(@$_REQUEST['backPage'])): ?>
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            window.addEventListener('unload', function (event) {
                if (window.parent == window.top) {
                    window.parent.$.magnificPopup.close();
                }
            });
        });
    </script>
<?php endif; ?>
