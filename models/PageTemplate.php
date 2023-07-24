<?php

namespace romanzaytsev\cms\models;

use phpDocumentor\Reflection\Types\Integer;
use romanzaytsev\cms\Module;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * This is the model class for table "pageTemplate".
 *
 * The followings are the available columns in table 'pageTemplate':
 * @property string $id
 * @property string $nameRu
 * @property string $nameEn
 * @property string $template
 * @property string $type
 * @property string $ready
 */
class PageTemplate extends \yii\db\ActiveRecord
{
    public $blocks;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return PageTemplate the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        $tablePrefix = Module::getTablePrefix();
        return "{{%{$tablePrefix}pageTemplate}}";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id', 'string', 'length' => [0.36]),
            array(['template', 'nameRu', 'nameEn'], 'string'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array(['id', 'nameRu', 'nameEn', 'ready'], 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'nameRu' => 'Имя',
            'nameEn' => 'Name'
        );
    }

    public function getName($language = null)
    {
        if (is_null($language)) {
            $language = @$_COOKIE['language'] ? $_COOKIE['language'] : 'Ru';
        }
        return @$this->{'name' . $language} ? $this->{'name' . $language} : $this->nameRu;
    }

    public static function getList($select = false)
    {
        $list = [];
        if ($select == 'notExist') {
            $existList = [];
            $existListArray = Page::find()->select(['pageTemplateId'])->distinct()->asArray()->all();
            foreach ($existListArray as $value) {
                $existList[] = @$value['pageTemplateId'];
            }
            $pageTemplates = self::getAll(true);
            foreach ($pageTemplates as $key=>$pageTemplate) {
                if($pageTemplate['type'] == 'single' && in_array($pageTemplate['id'], $existList)) {
                    unset($pageTemplates[$key]);
                }
            }
        } elseif ($select == 'multiple') {
            $pageTemplates = self::getAllByType('multiple');
        } else {
            $pageTemplates = self::getAll();
        }
        if (is_array($pageTemplates)) {
            foreach ($pageTemplates as $item) {
                $list[$item['id']] = $item['nameRu'];
            }
        }
        return $list;
    }

    public function getBlocks()
    {
        if (is_null($this->blocks)) {
            $this->blocks = \romanzaytsev\cms\models\PageTemplateBlock::find()
                ->where(['pageTemplateId' => $this->id])
                ->orderBy(['sort' => SORT_ASC])
                ->all();
        }
        return $this->blocks;
    }

    public static function getById($id) {
        $model = self::find()->where(['id' => $id])->one();
        $filePath = Yii::$app->controller->module->pageTemplatesPath . '/' . $id . '.php';
        if(empty($model) && file_exists($filePath)) {
            $returnAttributes = true;
            $attributes = require($filePath);
            if(isset($attributes['blocks']) && is_array($attributes['blocks'])) {
                foreach ($attributes['blocks'] as $key => $item) {
                    $attributes['blocks'][$key] = new PageTemplateBlock($item);
                }
            }
            $model = new self($attributes);
        }
        return $model;
    }

    public static function getAll($asArray=false) {
        $pageTemplates = self::find();
        if($asArray) {
            $pageTemplates->asArray();
        }
        $pageTemplates = $pageTemplates->all();
        $files = scandir(Yii::$app->controller->module->pageTemplatesPath);
        $returnAttributes = true;
        foreach ($files as $file) {
            $fileNameExplode = explode('.', $file);
            if(end($fileNameExplode) == 'php') {
                $attributes = require(Yii::$app->controller->module->pageTemplatesPath . '/' . $file);
                if($asArray) {
                    $pageTemplates[] = $attributes;
                } else {
                    if(isset($attributes['blocks']) && is_array($attributes['blocks'])) {
                        foreach ($attributes['blocks'] as $key => $item) {
                            $attributes['blocks'][$key] = new PageTemplateBlock($item);
                        }
                    }
                    $pageTemplates[] = new self($attributes);
                }
            }
        }
        return $pageTemplates;
    }

    public static function getAllByType($type) {
        $pageTemplates = self::find()->where(['type' => $type])->asArray()->all();
        $files = scandir(Yii::$app->controller->module->pageTemplatesPath);
        $returnAttributes = true;
        foreach ($files as $file) {
            $fileNameExplode = explode('.', $file);
            if(end($fileNameExplode) == 'php') {
                $attributes = require(Yii::$app->controller->module->pageTemplatesPath . '/' . $file);
                if($attributes['type'] == $type) {
                    $pageTemplates[] = $attributes;
                }
            }
        }
        return $pageTemplates;
    }
}
