<?php

namespace romanzaytsev\cms\models;

use romanzaytsev\cms\Module;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * This is the model class for table "PageBlockProperty".
 *
 * @property int $pageId
 * @property string $blockId
 * @property string $valueRu
 * @property string $valueEn
 */
class PageBlockProperty extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $tablePrefix = Module::getTablePrefix();
        return "{{%{$tablePrefix}pageBlockProperty}}";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pageId', 'blockId'], 'required'],
            [['pageId'], 'integer'],
            [['value'], 'string'],
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
            'value' => 'Value english',
        ];
    }

    public function getValue($prop)
    {
        $value = $this->value;
        if (@$prop['type'] == "textarea") {
            $value = str_replace(["\n"], '<br/>', $value);
        }
        return [
            'value' => $value,
        ];
    }

    public static function formFieldBlock($props = [])
    {
        extract($props);
        ob_start();
        echo Html::label($block['label'], $block['id'], ['class' => '']);
        switch (@$blocks[$block['id']]['prop']['type']) {
            case 'dropDownList':
            case 'dropDownListMultiple':
                $items = [null];
                $menuItems_parent = @$blocks[$block['id']]['prop']['menuItems'];
                if ($menuItems_parent) {
                    $menuItems = \romanzaytsev\cms\models\Ui::getAll($menuItems_parent);
                    foreach ($menuItems as $k => $v) {
                        $items[] = $v->valueRu;
                    }
                }
                $prop_items = (array)@$blocks[$block['id']]['prop']['items'];
                if ($prop_items && is_array($prop_items)) foreach (@$blocks[$block['id']]['prop']['items'] as $k => $v) {
                    $items[$k] = $v;
                }
                $isMultiple = $blocks[$block['id']]['prop']['type'] === 'dropDownListMultiple';
                $value = @$blocks[$block['id']]['data']['value'];
                if ($block['id'] === 'exclude_stage') {
                    $value = Json::decode($value);
                }
                echo Html::dropDownList('Page[updates][' . $block['type'] . '][' . $block['id'] . '][value]',
                    $value,
                    $items,
                    ['style' => 'display: block;', 'multiple' => $isMultiple]
                );
                break;
            case 'textarea':
                echo Html::textarea('Page[updates][' . $block['type'] . '][' . $block['id'] . '][value]', @$blocks[$block['id']]['data']['value'], ['style' => 'width:100%;height:160px;',]);
                break;
            case 'text':
            default:
                echo Html::textInput('Page[updates][' . $block['type'] . '][' . $block['id'] . '][value]', @$blocks[$block['id']]['data']['value'], ['style' => 'width:100%;', 'class' => 'form-control',]);
                break;
        }
        return ob_get_clean();
    }
}
