<?php

namespace romanzaytsev\cms\models;

use romanzaytsev\cms\Module;
use Yii;

/**
 * This is the model class for table "ui".
 *
 * @property string $parentId
 * @property string $id
 * @property string $valueRu
 * @property string $valueEn
 * @property string $href
 * @property int $sort
 */
class Ui extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $tablePrefix = Module::getTablePrefix();
        return "{{%{$tablePrefix}ui}}";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['sort'], 'integer'],
            [['parentId', 'id'], 'string', 'max' => 80],
            [['valueRu', 'valueEn'], 'string', 'max' => 255],
            [['href'], 'string', 'max' => 1024],
            [['id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parentId' => 'Parent ID',
            'id' => 'ID',
            'valueRu' => 'Value Ru',
            'valueEn' => 'Value En',
            'href' => 'Href',
            'sort' => 'Sort',
        ];
    }

    public static function getAll($parentId = null, $language = null, $level = 0)
    {
        if (is_null($language)) {
            $language = @$_COOKIE['language'] ? $_COOKIE['language'] : 'Ru';
        }
        $result = [];
        $array = self::find()->where(['parentId' => $parentId])->orderBy(['sort' => SORT_ASC])->asArray()->all();
        foreach ($array as $key => $item) {
            if ($level < 10) $item['childs'] = self::getAll($item['id'], $language, $level + 1);
            $item['value'] = $item['value' . $language];
            $result[] = (object)$item;
        }
        return $result;
    }

    public static function getOne($id = null, $language = null, $level = 0)
    {
        if (is_null($language)) {
            $language = @$_COOKIE['language'] ? $_COOKIE['language'] : 'Ru';
        }
        $result = null;
        $item = self::find()->where(['id' => $id])->orderBy(['sort' => SORT_ASC])->asArray()->one();
        if ($item) {
            if ($level < 10) $item['childs'] = self::getAll($item['id'], $language, $level + 1);
            $item['value'] = $item['value' . $language];
            $result = (object)$item;
        }
        return $result;
    }

    public function search($showHidden = 0)
    {
        $query = self::find();
        foreach ($this->attributes as $key => $value) {
            if (strlen($value)) $query->andFilterWhere(['LIKE', $key, $value]);
        }
        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            //'sort'=> ['defaultOrder' => ['sort'=>SORT_ASC]]
        ]);
        return $provider;
    }
}
