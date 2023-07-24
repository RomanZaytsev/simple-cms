<?php

use romanzaytsev\cms\models\Languages;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$langs = Languages::getAll();

?>

<style>
    .langbutton-container {
        float: right;
    }

    .langbutton {
        height: 20px;
        padding: 0px 5px 0px 5px;
        line-height: 0;
        outline: none;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .langbutton.active {
        border: 1px solid lightgray;
        background: white;
    }

    .langbox {
        overflow: hidden;
    }
</style>

<style id="tabStyle">
    .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
    }

    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
    }

    .tab button:hover {
        background-color: #ddd;
    }

    .tab button.active {
        background-color: #e3e3e3;
        font-weight: bold;
    }

    .tabcontent {
        display: none;
        padding: 6px 12px;
        border: 0px solid #ccc;
        border-top: none;
    }
</style>

<h1><?= is_null($model->id) ? "Новый альбом" : "Редактировать альбом" ?></h1>

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

    <div>
        <label class="" for="album-name">Название</label>
        <?php
        echo "<span class='langbutton-container'>";
        foreach (array_keys($langs) as $i => $lang):
            echo "<button type='button' onClick=\"changeLang(this, '" . $lang . "')\" class='langbutton " . ($i > 0 ? "" : "active") . "'>" . $lang . "</button>";
        endforeach;
        echo "</span>";
        foreach (array_keys($langs) as $i => $lang):
            echo "<div class='langbox langbox-" . $lang . "' style='" . ($i > 0 ? "height:0" : "") . ";'><div>";
            echo $form->field($model, 'name' . $lang)->label(false)->textInput([]);
            echo "</div></div>";
        endforeach;
        ?>
    </div>
    <div>
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

    <?= $form->field($model, 'preview')->label(true)->textInput([]); ?> <!-- Preview -->
    <?= $form->field($model, 'sort')->label(true)->textInput([]); ?> <!-- Сортировка -->
    <?php if (!$model->isNewRecord) echo $form->field($model, 'link')->label(true)->textInput([]); ?>


    <div id="albumBlocks">

    </div>
</div>
<div class="tabcontent" id="tab_metadata">
    Test
</div>
<br/>
<?= Html::submitButton((is_null($model->id) ? "Создать" : 'Сохранить'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'auth-button']) ?>

<?php ActiveForm::end(); ?>
