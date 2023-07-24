<?php

namespace romanzaytsev\cms;

use Yii;
use yii\base\BootstrapInterface;

class Module extends \yii\base\Module implements BootstrapInterface
{
    const DEFAULT_TABLE_PREFIX = 'cms';
    public $controllerNamespace = 'romanzaytsev\cms\controllers';
    public $pageTemplatesPath = null;
    public $tablePrefix = self::DEFAULT_TABLE_PREFIX;

    public function init()
    {
        parent::init();
        if(is_null($this->pageTemplatesPath)) {
            $this->pageTemplatesPath = __DIR__ . '/views/examplePageTemplates';
        }
    }

    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules([
            [
                'class' => 'yii\web\UrlRule',
                'pattern' => '<module:' . $this->id . '>',
                'route' => $this->id . '/page/list',
            ],
            [
                'class' => 'yii\web\UrlRule',
                'pattern' => '<module:' . $this->id . '>/<controller:\w+>/<action:\w+>',
                'route' => $this->id . '/<controller>/<action>',
            ],
            [
                'class' => 'yii\web\UrlRule',
                'pattern' => '<link:.*>',
                'route' => $this->id . '/page/view',
            ],
        ], false);
    }

    public static function getTablePrefix() {
        $module = Module::getInstance();
        if($module) {
            $tablePrefix = $module->tablePrefix;
        }
        else {
            $exceptionMessage = "\033[31m".'Module is not configured in the '.\Yii::$app->id.' configuration file'."\033[39m".PHP_EOL;
            echo $exceptionMessage;
            exit();
        }
        if(!empty($tablePrefix)) {
            $tablePrefix .= '_';
        }
        return $tablePrefix;
    }
}
