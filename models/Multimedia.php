<?php

namespace romanzaytsev\cms\models;

use romanzaytsev\cms\Module;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "multimedia".
 *
 * @property int $id
 * @property int $albumId
 * @property int $pageId
 * @property string $blockId
 * @property int $sort
 * @property string $nameRu
 * @property string $nameEn
 * @property string $descriptionRu
 * @property string $descriptionEn
 * @property string $link
 * @property string $href
 * @property string $preview
 * @property string $properties
 * @property string $fullTextRu
 * @property string $fullTextEn
 * @property string $date
 */
class Multimedia extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        $tablePrefix = Module::getTablePrefix();
        return "{{%{$tablePrefix}multimedia}}";
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort', 'albumId', 'pageId'], 'integer'],
            [['nameRu', 'nameEn'], 'string'],
            [['link', 'href', 'preview', 'blockId'], 'string'],
            [['descriptionRu', 'descriptionEn', 'properties', 'fullTextRu', 'fullTextEn'], 'string'],
            [['sort', 'albumId', 'albumId', 'pageId'], 'safe', 'on' => 'search'],
            [['date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'albumId' => 'album ID',
            'sort' => 'Сортировка',
            'nameRu' => 'Название',
            'nameEn' => 'Name',
            'descriptionRu' => 'Описание',
            'descriptionEn' => 'Description',
            'link' => 'Ссылка',
            'preview' => 'Превью-фото',
            'properties' => 'Свойства',
            'href' => 'Гиперссылка',
            'date' => 'Дата',
        ];
    }

    public function getValue($prop)
    {
        return [
            'id' => $this->id,
            'albumId' => $this->albumId,
            'sort' => $this->sort,
            'name' => $this->{"name" . $prop['lang']},
            'description' => $this->{"description" . $prop['lang']},
            'link' => $this->link,
            'preview' => $this->preview,
            'properties' => $this->properties,
            'href' => $this->href,
            'date' => $this->date,
        ];
    }

    public function search($params)
    {
        $query = self::find();
        if (isset($params['albumId'])) $query->andWhere(['albumId' => $params['albumId']]);
        if (isset($params['pageId'])) $query->andWhere(['pageId' => $params['pageId']]);
        if (isset($params['blockId'])) $query->andWhere(['blockId' => $params['blockId']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
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
        $text = @$this->{'name' . $language} ? $this->{'name' . $language} : $this->nameRu;
        $text = str_replace(["\n"], '<br/>', $text);
        return $text;
    }

    public function getDescription($language = null)
    {
        if (is_null($language)) {
            $language = @$_COOKIE['language'] ? $_COOKIE['language'] : 'Ru';
        }
        $text = @$this->{'description' . $language} ? $this->{'description' . $language} : $this->descriptionRu;
        $text = str_replace(["\n"], '<br/>', $text);
        return $text;
    }

    public function getFullText($language = null)
    {
        if (is_null($language)) {
            $language = @$_COOKIE['language'] ? $_COOKIE['language'] : 'Ru';
        }
        $text = @$this->{'fullText' . $language} ? $this->{'fullText' . $language} : $this->descriptionRu;
        return $text;
    }

    public function getProperties($name = null)
    {
        try {
            $obj = json_decode($this->properties);
            if (is_null($name)) {
                return $obj;
            } else {
                return $obj[$name];
            }
        } catch (\Exception $e) {

        }
    }

    public function getPreviewLink()
    {
        $preview = $this->preview;
        if (empty($preview)) {
            $preview = $this->link;
        }
        return $preview;
    }

    public static function formFieldBlock($props = [])
    {
        extract($props);
        ob_start();
        echo "<div class='form-group field-page-link'>";
        echo "<div>" . Html::label($block['label']) . "</div>";
        echo Html::button('Редактировать', [
            "onClick" => "$.magnificPopup.open({
                              items: {
                                src: '" . Url::to(['multimedia/' . @$blocks[$block['id']]['prop']['action'],
                    'isPopup' => 1,
                    'pageId' => $model->id,
                    'blockId' => $block['id'],
                    'table' => @$table
                ]) . '&' . @$blocks[$block['id']]['prop']['src']['parameters'] . "',
                                type: 'iframe'
                              }
                            });",
        ]);
        echo "</div>";
        return ob_get_clean();
    }
}
