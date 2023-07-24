<?php

use romanzaytsev\cms\models\PageTemplate;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;

AppAsset::register($this);
$this->registerCssFile(Yii::getAlias('@web') . '/css/admin.css?v=2', ['depends' => []]);
$this->registerJsFile(Yii::getAlias('@web') . '/js/admin-controller.js', ['depends' => []]);

use yii\bootstrap\BootstrapPluginAsset;

BootstrapPluginAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        .brand {
            color: #777;
            display: block;
            float: left;
            font-size: 20px;
            font-weight: 300;
            margin-left: -20px;
            padding: 10px 20px;
            text-shadow: 0 1px 0 #fff;
        }

        .sidebar li {
            list-style: none;
        }

        .sidebar li a {
            display: block;
            font-size: 16px;
            padding: 15px 0px 3px 0;
            text-decoration: none;
        }

        .sidebar {
            background-image: linear-gradient(to right, #fff, #e8e8e8);
            background-repeat: repeat-y;
            z-index: 1000;
            border-bottom: 1px solid #d6d6d6;
            border-right: 1px solid #d6d6d6;
            height: 100%;
            margin-right: 10px;
            padding: 2px 7px 0;
            position: fixed;
            width: 250px;
        }

        section {
            padding-top: 15px !important;
            padding-left: 250px !important;
            padding-right: 15px !important;
            max-width: 1000px !important;
        }

        .container,
        .navbar-static-top .container,
        .navbar-fixed-top .container,
        .navbar-fixed-bottom .container {
            width: 990px;
        }

        .main_column, .wrapper {
            width: auto;
        }

        .container {
            margin-left: 30px;
        }

        ul {
            padding: 0 0 0 15px;
        }

        ul ul {
            padding: 0 0 0 30px;
        }

        ul ul > li {
            list-style: initial !important;
        }

        ul ul a {
            #color: black !important;
            font-size: 14px !important;
            padding: 0 !important;
        }
    </style>

    <?php if (@$_REQUEST['isPopup']): ?>
        <style>
            .sidebar {
                display: none !important;
            }

            section {
                padding: 0 !important;
                max-width: 100% !important;
            }

            .container {
                margin-left: 0px !important;
                width: 100% !important;
            }
        </style>
    <?php endif; ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrapper">
    <div class="sidebar">
        <a class="brand" href="/">Simple CMS</a><br/><br/>
        <hr>
        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()): ?>
            <ul>
                <li>
                    <a href="<?= $this->context->URLto(["page/list"]) ?>">Страницы</a>
                    <ul>
                        <?php
                        $subPagesList = PageTemplate::getList('multiple');
                        if (is_array($subPagesList))
                            foreach ($subPagesList as $key => $name):
                                ?>
                                <li>
                                    <a href="<?= $this->context->URLto(["page/list", "Page[pageTemplateId]" => $key]) ?>"><?= $name ?></a>
                                </li>
                            <?php endforeach; ?>
                    </ul>
                </li>
                <li style="display:none;">
                    <a href="<?= $this->context->URLto(["ui/index"]) ?>">Элементы интерфейса</a>
                </li>
                <li>
                    <a href="<?= $this->context->URLto(["config/index"]) ?>">Конфиг</a>
                </li>
                <li>
                    <a href="<?= $this->context->URLto(["auth/logout"]) ?>">Выход</a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
    <section class="middle">
        <div class="container">
            <?= Alert::widget() ?>
            <div class="clearfix">

                <?php if (@$_REQUEST['backPage']):
                    echo "<br/>";
                    echo HTML::button('&#x21D0; Назад', [
                        'onClick' => " window.location.href = '" . @$_REQUEST['backPage'] . "'; ",
                        'class' => "btn-primary",
                    ]);
                    ?>
                <?php endif; ?>
                <?php echo $content; ?>
            </div><!-- /clearfix -->
        </div><!-- /container-->
    </section><!-- /middle-->
</div><!-- /wrapper -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
