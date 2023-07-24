<?php

namespace romanzaytsev\cms\models;

use romanzaytsev\cms\Module;
use Yii;

/**
 * This is the model class for table "pageTemplateBlock".
 *
 * @property string $pageTemplateId
 * @property string $Id
 * @property string $type
 * @property int $sort
 * @property string $label
 * @property string $properties
 */
class PageTemplateBlock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $tablePrefix = Module::getTablePrefix();
        return "{{%{$tablePrefix}pageTemplateBlock}}";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pageTemplateId', 'Id', 'properties'], 'required'],
            [['sort'], 'integer'],
            [['pageTemplateId'], 'string', 'max' => 40],
            [['Id', 'type'], 'string', 'max' => 45],
            [['label', 'properties'], 'string'],
            [['pageTemplateId', 'Id'], 'unique', 'targetAttribute' => ['pageTemplateId', 'Id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pageTemplateId' => 'Page Type ID',
            'Id' => 'ID',
            'type' => 'Type',
            'sort' => 'Sort',
            'properties' => 'properties'
        ];
    }

    public function getProperties()
    {
        if(!is_object($this->properties)) {
            try {
                $this->properties = json_decode($this->properties, true);
            } catch (\Exception $e) { }
        }
        return $this->properties;
    }
}
