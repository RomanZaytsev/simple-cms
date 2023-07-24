<?php

namespace romanzaytsev\cms\controllers;

use romanzaytsev\cms\models\Page;
use romanzaytsev\cms\models\PageTemplate;
use romanzaytsev\cms\models\PageTemplateBlock;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class PageController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->redirect(['page/list']);
    }

    public function actionList()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/index']);
        }
        if (!Yii::$app->user->isGuest && !Yii::$app->user->identity->isAdmin()) {
            Yii::$app->user->logout();
            $this->redirect(['auth/index']);
        }

        $model = new Page();
        if (isset($_GET['Page'])) {
            $model->attributes = $_GET['Page'];
        }
        $dataProvider = $model->search(@$_GET['showHidden']);

        $this->layout = 'admin';
        return $this->render('index', ['searchModel' => $model, 'dataProvider' => $dataProvider]);
    }

    public function actionDelete($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/index']);
        }
        if (!Yii::$app->user->isGuest && !Yii::$app->user->identity->isAdmin()) {
            Yii::$app->user->logout();
            $this->redirect(['auth/index']);
        }

        $model = $this->loadModel($id);

        $model->delete();
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function actionUpdate($id = null)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/index']);
        }
        if (!Yii::$app->user->isGuest && !Yii::$app->user->identity->isAdmin()) {
            Yii::$app->user->logout();
            $this->redirect(['auth/index']);
        }

        if (is_null($id)) {
            $model = new Page;
        } else {
            $model = $this->loadModel($id);
        }

        if (isset($_POST['Page'])) {
            @$_POST['Page']['link'] = trim(@$_POST['Page']['link']);
            $isNewRecord = $model->isNewRecord;
            if ($model->isNewRecord) {
                $_POST['Page']['link'] = Page::uniqueLink($_POST['Page']['nameRu']);
            }
            if (strrpos($_POST['Page']['link'], '/')) $_POST['Page']['link'] = '/' . ltrim($_POST['Page']['link'], '/');
            //$_POST['Page']['link'] = rtrim($_POST['Page']['link'], '/');
            $model->setAttributes($_POST['Page']);
            if (@$_GET['hideActions']) {
                $model->link = null;
            }
            if ($model->save()) {
                if ($isNewRecord) {
                    return $this->redirect(array_merge(['update', 'id' => $model->id], $_GET));
                } else {
                    return $this->refresh();
                }
            }
        }
        $pageTemplates = (object)["list" => []];
        $pageTemplate = $model->getPageTemplate();
        if ($pageTemplate) {
            $pageTemplates->list = [$pageTemplate->id => $pageTemplate->nameRu];
        } else {
            $pageTemplates->list = PageTemplate::getList($model->isNewRecord ? 'notExist' : '');
        }

        $this->layout = 'admin';
        return $this->render('update', ['model' => $model, 'pageTemplate' => $pageTemplate, 'blocks' => $model->getBlocks(), 'pageTemplates' => $pageTemplates]);
    }

    public function actionCase()
    {
        $model = Page::find()->where($_GET)->one();

        $this->layout = 'page';
        return $this->render('case', ['model' => $model]);
    }

    public function actionView()
    {
        $model = Page::findByURL(@$_GET['link']);

        if (empty($model)) {
            throw new \yii\web\NotFoundHttpException();
            die();
        } else {
            $category = @$model->getCategory();
            if (false && $category->ready != '+' && !@$_COOKIE['devmode2']) {
                echo "Страница в разработке</br><a href='/sitemap'>Список доступных страниц</a>";
                exit;
            }
        }
        $responseFormat = $_GET['responseFormat'] ?? null;
        if($responseFormat == 'json') {
            $data = [
                'page' => [
                    'template' => $model->getPageTemplate()->toArray(),
                    'blocks' => $model->getBlocks(),
                ],
                //'data' => $model->getData(),
            ];
            $response = Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_RAW;
            $response->getHeaders()->set('Content-Type', 'application/json; charset=utf-8');
            return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        $this->layout = $model->getPageTemplate()->layout;
        if (empty($model->getPageTemplate()->view)) {
            throw new \yii\web\NotFoundHttpException();
        }
        if (is_null($this->layout)) {
            return $this->renderPartial($model->getPageTemplate()->view, ['model' => $model]);
        } else {
            return $this->render($model->getPageTemplate()->view, ['model' => $model]);
        }
    }


    public function URLto($route)
    {
        return Url::to($route);
    }

    public function loadModel($id)
    {
        $model = Page::find()->where(['id' => $id])->one();
        if (is_null($model)) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }
}
