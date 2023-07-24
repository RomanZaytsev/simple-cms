<?php

namespace romanzaytsev\cms\models;

use romanzaytsev\cms\Module;
use Yii;

/**
 * This is the model class for table "languages".
 *
 * @property string $id
 * @property string $priority
 * @property string $value
 * @property string $img
 */
class Languages extends \yii\db\ActiveRecord
{
    public $img;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $tablePrefix = Module::getTablePrefix();
        return "{{%{$tablePrefix}languages}}";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['priority'], 'number'],
            [['id', 'value'], 'string', 'max' => 45],
            [['img'], 'file', 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'Value',
            'img' => 'Image',
            'priority' => 'приоритет',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->img) {
            $path = \Yii::getAlias('@webroot') . "/images/languages/";
            if (!is_dir($path)) mkdir($path, 0777, true);
            move_uploaded_file($this->img->tempName, $path . $this->id);
        }
    }

    public static function getAll()
    {
        $result = [];
        $array = Languages::find()->orderBy(['priority' => SORT_ASC])->asArray()->all();
        foreach ($array as $key => $item) {
            $result[$item["id"]] = $item["value"];
        }
        return $result;
    }

    public static function setAll($data)
    {
        $result = true;
        foreach ($data as $key => $value) {
            $model = self::find()->where(["id" => $key])->one();
            if ($model == null) {
                $model = new self;
                $model->id = $key;
            }
            $model->value = $value;
            if (!$model->save()) {
                $result = false;
            }
        }
        return $result;
    }
}
