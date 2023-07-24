<?php

namespace romanzaytsev\cms\models;

use romanzaytsev\cms\Module;
use Yii;

/**
 * This is the model class for table "config".
 *
 * @property string $id
 * @property string $value
 */
class Config extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $tablePrefix = Module::getTablePrefix();
        return "{{%{$tablePrefix}config}}";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'value'], 'string', 'max' => 45],
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
        ];
    }

    public static function getAll()
    {
        $result = [];
        $array = Config::find()->asArray()->all();
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
