<?php

namespace romanzaytsev\cms\controllers;

use romanzaytsev\cms\models\Album;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class MultimediaController extends Controller
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
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/index']);
        }
        if (!Yii::$app->user->isGuest && !Yii::$app->user->identity->isAdmin()) {
            Yii::$app->user->logout();
            $this->redirect(['auth/index']);
        }

        $model = new album();

        if (isset($_GET['album'])) {
            $model->attributes = $_GET['album'];
        }
        $dataProvider = $model->search();
        $dataProvider->pagination = array('pageSize' => false,);
        $dataProvider->sort = array('defaultOrder' => 'sort DESC');

        $this->layout = 'admin';
        return $this->render('index', ['searchModel' => $model, 'dataProvider' => $dataProvider]);
    }

    public function actionList()
    {
        $table = "Multimedia";
        if (isset($_GET['table'])) {
            $table = $_GET['table'];
        }
        $CLASS = ("romanzaytsev\\cms\\models\\" . $table);

        $searchModel = new $CLASS;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = array('defaultOrder' => 'sort DESC');

        $model = Album::find()->where(['id' => @$_GET['albumId']])->one();;

        $this->layout = 'admin';
        return $this->render('list', ['model' => $model, 'table' => $table, 'searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
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

        $table = "Multimedia";
        if (isset($_GET['table'])) {
            $table = $_GET['table'];
        }

        $model = $this->loadModel($id, $table);

        $model->delete();
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function actionItem($id = null)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/index']);
        }
        if (!Yii::$app->user->isGuest && !Yii::$app->user->identity->isAdmin()) {
            Yii::$app->user->logout();
            $this->redirect(['auth/index']);
        }

        $table = "Multimedia";
        if (isset($_GET['table'])) {
            $table = $_GET['table'];
        }
        $CLASS = ("\\romanzaytsev\\cms\\models\\" . $table);

        $attr = [];
        if (isset($_GET['id'])) {
            $attr['id'] = @$_GET['id'];
        };
        if (isset($_GET['albumId'])) {
            $attr['albumId'] = @$_GET['albumId'];
        };
        if (isset($_GET['pageId'])) {
            $attr['pageId'] = @$_GET['pageId'];
        };
        if (isset($_GET['blockId'])) {
            $attr['blockId'] = @$_GET['blockId'];
        };
        $model = $CLASS::find()->where($attr)->one();

        if (empty($model)) {
            $model = new $CLASS;
            $model->attributes = $_GET;
        }

        $POST_DATA = @$_POST[$table];
        if (is_array(@$POST_DATA)) {
            $isNewRecord = $model->isNewRecord;
            $model->attributes = $POST_DATA;
            if ($model->save()) {
                if (@$_REQUEST['backPage']) {
                    return $this->redirect($_REQUEST['backPage']);
                }
                if ($isNewRecord) {
                    return $this->redirect(['item', 'id' => $model->id]);
                } else {
                    return $this->refresh();
                }
            }
        }
        $this->layout = 'admin';

        $args = ['model' => $model];

        return $this->render('item', $args);
    }

    public function actionAlbum($id = null)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/index']);
        }
        if (!Yii::$app->user->isGuest && !Yii::$app->user->identity->isAdmin()) {
            Yii::$app->user->logout();
            $this->redirect(['auth/index']);
        }

        if (is_null($id)) {
            $model = new Album;
        } else {
            $model = $this->loadModel($id, 'Album');
        }

        $POST_DATA = @$_POST['Album'];
        if (isset($POST_DATA)) {
            if ($model->isNewRecord) {
                $POST_DATA['link'] = album::uniqueLink($POST_DATA['nameRu']);
            }
            $POST_DATA['link'] = rtrim($POST_DATA['link'], '/');
            $model->attributes = $POST_DATA;
            if ($model->save()) {
                return $this->redirect(['album', 'id' => $model->id]);
            }
        }
        $this->layout = 'admin';
        //print_r( $model->Block ); exit;
        return $this->render('album', ['model' => $model]);
    }

    public function actionDeletealbum($id = null)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/index']);
        }
        if (!Yii::$app->user->isGuest && !Yii::$app->user->identity->isAdmin()) {
            Yii::$app->user->logout();
            $this->redirect(['auth/index']);
        }

        $model = $this->loadModel($id, 'Album');
        $model->delete();
        return $this->redirect(Yii::$app->request->referrer ?: 'index');
    }

    public function URLto($route)
    {
        return Url::to($route);
    }

    public function loadModel($id, $class = 'Multimedia')
    {
        $model = ('romanzaytsev\\cms\\models\\' . $class)::find()->where(['id' => $id])->one();
        if (is_null($model)) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }
}
