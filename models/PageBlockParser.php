<?php

namespace romanzaytsev\cms\models;

use romanzaytsev\cms\Module;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use Rct567\DomQuery\DomQuery;

/**
 * This is the model class for table "PageBlockParser".
 *
 * @property int $pageId
 * @property string $blockId
 * @property string $url
 * @property string $selector
 * @property string $regexp
 */
class PageBlockParser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $tablePrefix = Module::getTablePrefix();
        return "{{%{$tablePrefix}pageBlockParser}}";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pageId', 'blockId'], 'required'],
            [['pageId'], 'integer'],
            [['url', 'selector', 'regexp'], 'string'],
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
            'url' => 'Url',
            'selector' => 'Selector',
            'regexp' => 'Regexp',
        ];
    }

    public function getValue($prop)
    {
        $error = null;
        try {
            $content = file_get_contents($this->url);
            $dom = new DomQuery($content);
            $element = $dom->find($this->selector)->first()->text();
            preg_match($this->regexp, $element, $matches);
        } catch(\Exception $e) {
            $error = $e->getMessage();
        }
        return [
            'url' => $this->url,
            'selector' => $this->selector,
            'regexp' => $this->regexp,
            'value' => $matches[0] ?? null,
            'error' => $error,
        ];
    }

    public static function formFieldBlock($props = [])
    {
        extract($props);
        ob_start();
        echo Html::label($block['label'], $block['id'], ['class' => '']);
        echo Html::textInput(
            'Page[updates][' . $block['type'] . '][' . $block['id'] . '][url]',
            @$blocks[$block['id']]['data']['url'],
            ['class' => 'form-control', 'style' => 'margin:0 0 10px 0;', 'placeholder' => 'Url']);
        echo Html::textInput(
            'Page[updates][' . $block['type'] . '][' . $block['id'] . '][selector]',
            @$blocks[$block['id']]['data']['selector'],
            ['class' => 'form-control', 'style' => 'margin:0 0 10px 0;', 'placeholder' => 'Selector']
        );
        echo Html::textInput(
            'Page[updates][' . $block['type'] . '][' . $block['id'] . '][regexp]',
            @$blocks[$block['id']]['data']['regexp'],
            ['class' => 'form-control', 'style' => 'margin:0 0 10px 0;', 'placeholder' => 'RegExp']
        );
        return ob_get_clean();
    }
}
