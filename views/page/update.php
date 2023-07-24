<?php

use romanzaytsev\cms\models\Languages;
use romanzaytsev\cms\models\Page;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;

$langs = Languages::getAll();
?>

<h1></h1>

<?= $this->context->renderPartial('/parts/tab-content', ['model' => $model]); ?>

<?php $form = ActiveForm::begin(['id' => 'news-form', 'enableClientValidation' => false]); ?>

<div class="has-error">
    <div class="help-block">
        <?= $form->errorSummary($model); ?>
    </div>
</div>

<div class="tabcontent" id="tab_content">
    <div>
        <label class="" for="page-name">Название</label>
        <?= $this->context->renderPartial('/parts/langbutton-container', [
                'langs' => $langs,
                'formField' => function ($lang) use ($form, $model) {
                    return $form->field($model, 'name' . $lang)->label(false)->textInput(['autocomplete' => 'off']);
                },
            ]
        ); ?>
    </div>

    <?= $form->field($model, 'pageTemplateId')->dropDownList(
        $pageTemplates->list,    // Flat array ('id'=>'label')
        ['prompt' => '', 'value' => @$pageTemplate->id, 'disabled' => ($model->isNewRecord ? false : 'disabled')]    // options
    ); ?>
    <?php if (!$model->isNewRecord && !@$_GET['hideActions']) echo $form->field($model, 'link')->label(true)->textInput(['autocomplete' => 'off']); ?>
    <div id="pageBlocks">
        <?php if (isset($pageTemplate)): ?>
            <?php foreach ($pageTemplate->blocks as $block): ?>
                <div class="form-group">
                    <?php
                    switch ($block['type']) {
                        default:
                            $blockTable = "romanzaytsev\\cms\\models\\" . $block['type'];
                            echo $blockTable::formFieldBlock([
                                'form' => $form,
                                'model' => $model,
                                'blocks' => $blocks,
                                'block' => $block,
                                'langs' => $langs,
                            ]);
                            break;
                        case 'parentPageId':
                            echo "" . $form->field($model, 'parentPageId')->dropDownList(
                                    Page::getList((array)@$blocks[$block['id']]['prop']['where']),    // Flat array ('id'=>'label')
                                    @$blocks[$block['id']]['prop']['offDefault'] ? [] : ['prompt' => '']    // options
                                );
                            break;
                        case 'publication_date':
                            if ($model->id) echo $form->field($model, 'datePublications')->widget(DateTimePicker::classname(), [
                                'options' => ['placeholder' => 'Enter publication date', 'autocomplete' => 'off'],
                                'type' => DateTimePicker::TYPE_INPUT,
                                'pluginOptions' => [
                                    'autoclose' => true
                                ]
                            ]);
                            break;
                    } ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (!$model->isNewRecord): ?>
            <?= $form->field($model, 'sort')->label(true)->textInput(['type' => 'number']); ?>
        <?php endif; ?>
    </div>
</div>

<?= $form->field($model, 'hidden')->label(false)->hiddenInput([]) ?>
<br/>
<?= Html::submitButton((is_null($model->id) ? "Создать" : 'Сохранить'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'auth-button']) ?>

<?php ActiveForm::end(); ?>
