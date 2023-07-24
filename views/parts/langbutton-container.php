<?php
$style = <<< CSS
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
CSS;
$this->registerCss($style, [], 'langbutton-container');

$jsScript = <<< JS
    function changeLang(self, lang) {
        $(self).closest('div').find('.langbox').css('height', '0');
        $(self).closest('div').find('.langbox-' + lang).css('height', '');
        $(self).closest('div').find('.langbutton').removeClass('active');
        $(self).addClass('active');
    }
JS;
$this->registerJs($jsScript, \yii\web\View::POS_HEAD);
?>

<?php
echo "<span class='langbutton-container'>";
foreach (array_keys($langs) as $i=>$lang):
    echo "<button type='button' onClick=\"changeLang(this, '".$lang."')\" class='langbutton ".($i>0?"":"active")."'>".$lang."</button>";
endforeach;
echo "</span>";
foreach (array_keys($langs) as $i=>$lang):
    echo "<div class='langbox langbox-".$lang."' style='".($i>0?"height:0":"").";'><div>";
    echo $formField($lang);
    echo "</div></div>";
endforeach;
?>