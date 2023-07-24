<?php

namespace romanzaytsev\cms\models;

use romanzaytsev\cms\Module;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "PageBlockText".
 *
 * @property int $pageId
 * @property string $blockId
 * @property string $valueRu
 * @property string $valueEn
 */
class PageBlockText extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $tablePrefix = Module::getTablePrefix();
        return "{{%{$tablePrefix}pageBlockText}}";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pageId', 'blockId'], 'required'],
            [['pageId'], 'integer'],
            [['valueRu', 'valueEn'], 'string'],
            [['blockId'], 'string', 'max' => 45],
            [['pageId', 'blockId'], 'unique', 'targetAttribute' => ['pageId', 'blockId']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pageId' => 'Page ID',
            'blockId' => 'Block ID',
            'valueRu' => 'Value',
            'valueEn' => 'Value english',
        ];
    }

    public function getValue($prop)
    {
        $value = null;
        if (isset($this->{"value" . $prop['lang']})) {
            $value = $this->{"value" . $prop['lang']};
        }
        if (empty($value)) {
            $value = $this->valueRu;
        }
        switch (@$prop['type']) {
            case "textarea":
                $value = str_replace(["\n"], '<br/>', $value);
                break;
        }
        return [
            'value' => $value,
        ];
    }

    public static function formField($lang)
    {
    }

    public static function formFieldBlock($props = [])
    {
        extract($props);
        ob_start();
        echo Html::label($block['label'], $block['id'], ['class' => '']);
        echo Yii::$app->controller->renderPartial('/parts/langbutton-container', [
            'langs' => $langs,
            'formField' => function ($lang) use ($props) {
                extract($props);
                ob_start();
                switch (@$blocks[$block['id']]['prop']['type']) {
                    case 'wysiwyg':
                        echo \vova07\imperavi\Widget::widget([
                            'name' => 'Page[updates][' . $block['type'] . '][' . $block['id'] . '][value' . $lang . ']',
                            'value' => @$blocks[$block['id']]['data']['value' . $lang],
                            'options' => [
                                'lang' => 'ru',
                                'imageUpload' => '/admin/file/upload?dir=image/',
                                'fileUpload' => '/admin/file/upload?dir=file/',
                                'minHeight' => "320px",
                                'replaceDivs' => false,
                            ],
                        ]);
                        break;
                    case 'html':
                    case 'textarea':
                        echo Html::textarea('Page[updates][' . $block['type'] . '][' . $block['id'] . '][value' . $lang . ']', @$blocks[$block['id']]['data']['value' . $lang], ['style' => 'width:100%;height:160px;',]);
                        break;
                    case 'text':
                    default:
                        echo Html::textInput('Page[updates][' . $block['type'] . '][' . $block['id'] . '][value' . $lang . ']', @$blocks[$block['id']]['data']['value' . $lang], ['style' => 'width:100%;', 'class' => 'form-control',]);
                        break;
                }
                return ob_get_clean();
            },
        ]);
        return ob_get_clean();
    }
}
