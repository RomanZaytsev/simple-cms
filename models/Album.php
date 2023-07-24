<?php

namespace romanzaytsev\cms\models;

use romanzaytsev\cms\Module;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "album".
 *
 * @property int $id
 * @property int $sort
 * @property string $nameRu
 * @property string $nameEn
 * @property string $descriptionRu
 * @property string $descriptionEn
 * @property string $link
 * @property string $preview
 */
class Album extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        $tablePrefix = Module::getTablePrefix();
        return "{{%{$tablePrefix}album}}";
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort'], 'integer'],
            [['nameRu', 'nameEn'], 'string'],
            [['link', 'preview'], 'string'],
            [['descriptionRu', 'descriptionEn'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sort' => 'Сортировка',
            'nameRu' => 'Имя',
            'nameEn' => 'Name',
            'descriptionRu' => 'Описание',
            'descriptionEn' => 'Description',
            'link' => 'Ссылка',
            'preview' => 'Превью-фото',
        ];
    }

    public function search()
    {
        $query = self::find();
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
        ]);
        return $provider;
    }

    public function uniqueLink($string)
    {
        $link = Yii::$app->MyTools::makeLinkUrl($string);
        $sufix = "";
        $increment = 1;
        $exists = true;
        while ($exists) {
            $result = $link . $sufix;
            $exists = self::find()
                ->where(["link" => $result])
                ->exists();
            $sufix = "_" . ($increment++);
        }
        return $result;
    }

    public function getName($language = null)
    {
        if (is_null($language)) {
            $language = @$_COOKIE['language'] ? $_COOKIE['language'] : 'Ru';
        }
        return @$this->{'name' . $language} ? $this->{'name' . $language} : $this->nameRu;
    }
}
