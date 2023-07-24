<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Войти в систему';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<div class="wrap">
<div class="login-box">
    <div></div>
    <div class="login-box-body loginblock block m-40">
        <h2 class="login-box-msg">Войти в систему</h2>

        <?php $form = ActiveForm::begin(['id' => 'auth-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <div class="row">
            <div class="col-xs-8">
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
            </div>
            <div class="col-xs-4">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'auth-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
</div>

<script>
window.addEventListener("load", function() {
  $("#loginform-username").focus();
});
</script>
