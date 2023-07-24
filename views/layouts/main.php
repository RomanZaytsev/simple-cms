<?php

use app\components\Lang;
use app\models\Ui;
use yii\helpers\Html;
use app\assets\AppAsset;
use yii\web\View;

AppAsset::register($this);

/** @var View $this */

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/html" xml:lang="ru" version="XHTML+RDFa 1.0">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=yes">

		<?php $this->head() ?>
    <script type="text/javascript" src="/js/MyTools.js"></script>
      <link rel="stylesheet" type="text/css" href="/css/style.css?v=4" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/vnd.microsoft.icon">
    <title><?= Html::encode($this->title) ?></title>
  </head>
	<body>
  <?php $this->beginBody() ?>
		<?= $content ?>
  <?php $this->endBody() ?>
  </body>
</html>
<?php $this->endPage() ?>
