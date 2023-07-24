<?php

namespace romanzaytsev\cms\models;

use romanzaytsev\cms\Module;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "Page".
 *
 * The followings are the available columns in table 'Page':
 * @property string $id
 * @property string $nameRu
 * @property string $nameEn
 * @property string $link
 * @property string $datePublications
 * @property string $preview
 * @property integer $publish
 * @property string $tagDescription
 * @property integer $views
 * @property array $blocks
 * @property array $updates
 * @property string $parentPageId
 * @property string $hidden
 * @property integer $sort
 *
 * @method Page published() Опубликованные новости
 */
class Page extends \yii\db\ActiveRecord
{
    public $blocks;
    public $template;
    public $data;
    public $updates;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Page the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function tableName()
    {
        $tablePrefix = Module::getTablePrefix();
        return "{{%{$tablePrefix}page}}";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(['nameRu', 'pageTemplateId'], 'required'),
            array(['publish', 'views'], 'number', 'integerOnly' => true),
            array('id', 'number'),
            array(['link', 'nameRu', 'nameEn', 'tagDescription', 'pageTemplateId'], 'string', 'length' => [0, 150]),
            array(['datePublications', 'Text', 'preview', 'Date', 'hidden', 'sort'], 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array(['id', 'link', 'datePublications', 'Text', 'preview', 'publish', 'tagDescription', 'pageTemplateId'], 'safe', 'on' => 'search'),
            array(['shareButtonsTitle', 'allTagsString', 'parentPageId'], 'safe'),
            array('mainImage', 'file', 'extensions' => ['png', 'jpg', 'gif']),
            [['hidden'], 'default', 'value' => 0],
            // other rules ...
            [['blocks', 'updates'], 'safe'],
        );
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            return true;
        }
        return false;
    }

    public function getType()
    {
        return $this->hasOne(PageTemplate::className(), ['id' => 'pageTemplateId']);
    }


    /**
     * @return array customized attribute labels (nameRu=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'nameRu' => 'Название',
            'link' => 'Ссылка',
            'datePublications' => 'Дата публикации',
            'Date' => 'Дата создания',
            'Text' => 'Текст',
            'preview' => 'Анонс',
            'pageTemplateId' => 'Шаблон',
            'parentPageId' => 'Родительская страница',
            'publish' => 'Опубликовано',
            'tagDescription' => 'Тэг description',
            'allTagsString' => 'Теги (через пробел)',
            'mainImage' => 'Превью-картинка',
            'sort' => 'Сортировка'
        );
    }

    public function search($showHidden = 0)
    {
        $query = Page::find();
        foreach ($this->attributes as $key => $value) {
            if (strlen($value)) $query->andFilterWhere(['LIKE', $key, $value]);
        }
        if (@!$showHidden) {
            $query->andFilterWhere(['!=', 'hidden', '1']);
        }
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'sort' => ['defaultOrder' => ['datePublications' => SORT_DESC]]
        ]);
        return $provider;
    }

    public function isPublished()
    {
        return $this->publish = 1 and $this->datePublications < NOW();
    }

    public function getMyDate()
    {
        return date('d.m.Y', strtotime($this->datePublications));
    }

    public function getPageTemplate()
    {
        if (empty($this->template)) {
            $this->template = PageTemplate::getById($this->pageTemplateId);
        }
        return $this->template;
    }

    public function getEditedText()
    {
        $arr = explode("\n", $this->Text);
        foreach ($arr as $a) {
            $a = "<p>" . $a . "</p>";
            $text .= $a;
        }
        return $text;
    }

    public function getFormattedText()
    {
        $output_text = str_replace('<a', '<a rel="nofollow"', $this->Text);
        return $output_text;
    }

    public function getMainImage()
    {
        if (!empty($this->mainImage)) {
            return $this->mainImage;
        }
        try { // Добавляет метатег для репоста на фейсбук и ВК
            $pattern = "/<img.+?src=[\"'](.+?)[\"'].*?>/";
            if (preg_match($pattern, $this->Text, $imgsrc)) {
                return $imgsrc[1];
            }
        } catch (Exception $e) {
        }
        return null;
    }

    public static function getLastPage($limit = 0, $offset = 0, $excludeId = 0)
    {
        $filter = array(
            'limit' => $limit,
            'offset' => $offset,
            'order' => 'Date DESC'
        );
        if ($excludeId) {
            $filter['condition'] = " id != '{$excludeId}' ";
        }
        return Page::model()->published()->findAll($filter);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $blocks = $this->getBlocks();
                if (is_array($blocks)) {
                    if (is_array($blocks))
                        foreach ($blocks as $type => $block) {
                            if ($type == "default") {
                                if (is_array($block))
                                    foreach ($block as $obj) {
                                        if (isset($obj->prop))
                                            foreach ($obj->prop as $key => $value) {
                                                $this->{$key} = $value;
                                            }
                                    }
                            }
                        }
                }
            }

            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (is_array($this->updates)) {
            foreach ($this->updates as $type => $block) {
                $blockTable = "romanzaytsev\\cms\\models\\" . $type;
                if (class_exists($blockTable))
                    foreach ($block as $blockId => $obj) {
                        $model = $blockTable::find()->where(["pageId" => $this->id, "blockId" => $blockId])->one();
                        if (!$model) {
                            $model = new $blockTable;
                            $model->pageId = $this->id;
                            $model->blockId = $blockId;
                        }
                        $model->attributes = $obj;
                        $model->save();
                    }
            }
        }
    }

    public static function uniqueLink($string)
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

    public function beforeDelete()
    {
        PageBlockText::deleteAll(['pageId' => $this->id]);
        PageBlockParser::deleteAll(['pageId' => $this->id]);
        PageBlockProperty::deleteAll(['pageId' => $this->id]);
        return parent::beforeDelete();
    }

    public function getBlocks()
    {
        if (is_null($this->blocks)) {
            $this->blocks = [];
            $blocks = $this->getPageTemplate() ? $this->getPageTemplate()->getBlocks() : [];
            foreach ($blocks as $block) {
                if (@isset($this->blocks[$block->type][$block->id])) continue;
                $blockTable = "romanzaytsev\\cms\\models\\" . $block->type;
                $prop = $block->getProperties();
                if ($prop) $prop['lang'] = $this->getLanguage();
                $blockData = [];
                if (class_exists($blockTable) && (new $blockTable)->hasAttribute("pageId")) {
                    $action = ($prop['action'] ?? null) == 'list' ? 'all' : 'one';
                    $blockData = $blockTable::find()->where(["pageId" => $this->id, "blockId" => $block->id])->{$action}();
                }
                $this->blocks[$block->id] = [
                    'type' => $block->type,
                    'data' => $blockData,
                    'prop' => $prop,
                ];
            }
        }
        return $this->blocks;
    }

    public function getData()
    {
        $result = [];
        $blocks = $this->getBlocks();
        foreach ($blocks as $blockId => $block) {
            $data = $block['data'];
            if(!is_array($block['data'])) {
                if (is_object($data)) {
                    $result[$blockId] = $data->getValue((object)$block['prop']);
                } else {
                    $result[$blockId] = null;
                }
            } else {
                foreach ($block['data'] as $key=>$data) {
                    if (is_object($data)) {
                        $result[$blockId][$key] = $data->getValue((object)$block['prop']);
                    } else {
                        $result[$blockId][$key] = null;
                    }
                }
            }
        }
        return $result;
    }

    public function getLanguage()
    {
        return @$_COOKIE['language'] ? $_COOKIE['language'] : 'Ru';
    }

    public function getBlockValue($blockId, $one = 'one')
    {
        $blocks = $this->getPageTemplate()->getBlocks();
        foreach ($blocks as $item) {
            if($item->id == $blockId) {
                $block = $item;
                break;
            }
        }
        if (!isset($block)) {
            return null;
        }
        $blockTable = "romanzaytsev\\cms\\models\\" . $block->type;
        if (class_exists($blockTable)) {
            $query = $blockTable::find()->where(["pageId" => $this->id, "blockId" => $blockId]);
            if ((new $blockTable)->hasAttribute('sort')) $query->orderBy(['sort' => SORT_ASC]);
            $blockModel = $query->{$one}();
            if ($blockModel) {
                $prop = $block->getProperties();
                if ($prop) $prop['lang'] = $this->getLanguage();
                $result = [];

                if (is_array($blockModel)) {
                    foreach ($blockModel as $blockItem) {
                        $result[] = $blockItem->getValue($prop);
                    }
                } else {
                    $result = $blockModel->getValue($prop);
                }
                return $result['value'];
            }
        }
    }

    public function getName($language = null)
    {
        $language = $this->getLanguage();
        return @$this->{'name' . $language} ? $this->{'name' . $language} : $this->nameRu;
    }

    public function getUrl($absolute = null)
    {
        //$this->link = rtrim($this->link, '/');
        if (strlen($this->link) && $this->link[0] != '/') {
            /*$parent = self::find()->where(['id'=>$this->parentPageId])->one();
            if($parent) {
                $url = $parent->getUrl().'/'.$url;
            }*/
            if (is_null($this->parentPageId)) {
                $url = '/' . $this->link;
            } else {
                $parent = Page::find()->where([
                    'id' => $this->parentPageId,
                ])->one();
                if ($parent) {
                    $parentUrl = rtrim($parent->getUrl(), '/');
                    $url = $parentUrl . '/' . $this->link;
                }
            }
        } else {
            $url = $this->link;
        }
        if ($absolute) $url = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http') . "://" . $_SERVER['SERVER_NAME'] . $url;

        return $url;
    }

    public function getBreadcrumbs($language = null)
    {
        $language = $this->getLanguage();
        $url = "/";
        $result = [];

        $page = Page::findByURL($url);
        $result[] = (object)[
            'name' => @$page->getName(),
            'link' => @$page->getUrl(),
        ];

        $link_arr = explode('/', trim(@$this->getUrl(), '/'));
        foreach ($link_arr as $link) {
            if (!empty($link)) {
                $url .= "/" . $link;
                $page = Page::findByURL($url);
                if ($page) {
                    $result[] = (object)[
                        'name' => @$page->getName(),
                        'link' => $page->getUrl(),
                    ];
                }
            }
        }
        return $result;
    }

    public static function findByURL($url)
    {
        $link = trim($url, '/');
        $where = ['link' => '/' . $link];
        $model = Page::find()->where($where)->one();
        if (empty($model)) {
            $pagelink = trim(substr($link, strrpos($link, '/')), '/');
            $models = Page::find()->where(['link' => $pagelink])->all();
            foreach ($models as $model) {
                $newLink = trim($model->getUrl(), '/');
                if ($newLink === $link) break;
                else unset($model);
            }
        }
        return @$model;
    }

    public function getCategory()
    {
        return \romanzaytsev\cms\models\PageTemplate::getById($this->pageTemplateId);
    }

    public static function getList($where = [])
    {
        $list = [];
        $items = self::find()->where($where)->asArray()->all();
        foreach ($items as $item) {
            $list[$item['id']] = $item['nameRu'];
        }
        return $list;
    }

    public function findChilds()
    {
        return self::find()->where(['parentPageId' => $this->id]);
    }

    public static function formFieldBlock($props = [])
    {
        extract($props);
        ob_start();
        echo "<div class='form-group field-page-link'>";
        echo "<div>" . Html::label($block['label']) . "</div>";
        echo Html::button('Редактировать', [
            "onClick" => "window.location='" . Url::to(['page/' . @$blocks[$block['id']]['prop']['action'],
                    'showHidden' => 1,
                    'backPage' => @$_SERVER['REQUEST_URI'],
                ]) . (@$blocks[$block['id']]['prop']['src']['withParentPage'] ? '&Page[parentPageId]=' . $model->id : '') . '&' . @$blocks[$block['id']]['prop']['src']['parameters'] . "'",
        ]);
        echo "</div>";
        return ob_get_clean();
    }
}
