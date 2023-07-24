<?php

namespace romanzaytsev\cms\controllers;

use romanzaytsev\cms\models\Config;
use romanzaytsev\cms\models\Ui;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class ConfigController extends Controller
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
        if (Yii::$app->user->isGuest) {
            $this->redirect(['auth/index']);
        }
        if (!Yii::$app->user->isGuest && !Yii::$app->user->identity->isAdmin()) {
            Yii::$app->user->logout();
            $this->redirect(['auth/index']);
        }
        $this->layout = 'admin';
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
        $class = Config::className();
        $table = 'Config';
        $this->view->title = 'Config';
        if (isset($_GET['table']) && $table != 'Config') {
            $table = $_GET['table'];
            $class = ("romanzaytsev\\cms\\models\\" . $table);
            $this->view->title = $table;
        }
        $list = $class::find()->orderBy("id")->all();
        return $this->render('index', ["table" => $table, "list" => $list]);
    }

    public function actionEdit()
    {
        $table = isset($_GET['table']) ? $_GET['table'] : 'Config';
        $class = $this->getConfigClass($table);
        $this->view->title = $table;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $model = $class::find()->where(["id" => $id])->one();
        } else {
            $model = new $class();
        }

        $childsValue = [];
        if (method_exists($model, 'getChildsValue')) {
            $childsValue = $model->getChildsValue();
        }
        if (Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            foreach ($model->getValidators() as $validator) {
                if (isset($validator->maxFiles)) {
                    foreach ($validator->attributes as $attr) {
                        $model->{$attr} = \yii\web\UploadedFile::getInstance($model, $attr);
                    }
                }
            };
            $result = "";
            if ($model->save()) {
                if (@$_GET['layout'] == 'admin') {
                    echo "<script>window.history.go(-2);</script>";
                    exit;
                } else {
                    $result = [
                        "validate" => [],
                        "status" => "OK",
                    ];
                }
            }
            echo json_encode($result);
            exit;
        }
        if (@$_GET['layout'] == 'admin') {
            return $this->render('edit', ["model" => $model, "childsValue" => $childsValue]);
        }
        return $this->renderPartial('edit', ["model" => $model, "childsValue" => $childsValue]);
    }

    public function actionDelete()
    {
        $table = isset($_GET['table']) ? $_GET['table'] : 'Config';
        $class = $this->getConfigClass($table);
        $id = $_REQUEST['id'];
        $model = $class::find()->where(["id" => $id])->one();
        if ($model->delete()) {
            $result = [
                "validate" => [],
                "status" => "OK",
                "success" => 1
            ];
            echo json_encode($result);
            exit;
        }
    }

    public function URLto($route)
    {
        return Url::to($route);
    }


    public function actionUi()
    {
        $this->view->title = 'Ui';
        $model = new Ui;
        if (isset($_GET['Ui'])) {
            $model->attributes = $_GET['Ui'];
        }
        $dataProvider = $model->search();

        return $this->render('ui', ['searchModel' => $model, 'dataProvider' => $dataProvider]);
    }

    public function actionUiDelete($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/index']);
        }
        if (!Yii::$app->user->isGuest && !Yii::$app->user->identity->isAdmin()) {
            Yii::$app->user->logout();
            $this->redirect(['auth/index']);
        }

        $model = Ui::find()->where(['id' => $id])->one();
        $model->delete();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function actionUiUpdate($id = null)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/index']);
        }
        if (!Yii::$app->user->isGuest && !Yii::$app->user->identity->isAdmin()) {
            Yii::$app->user->logout();
            $this->redirect(['auth/index']);
        }

        if (isset($_GET['id'])) {
            $attr = [];
            $attr['id'] = @$_GET['id'];
            $model = Ui::find()->where($attr)->one();
        };

        if (empty($model)) {
            $model = new Ui;
            $model->attributes = $_GET;
        }

        $POST_DATA = @$_POST['Ui'];
        if (is_array(@$POST_DATA)) {
            $isNewRecord = $model->isNewRecord;
            $model->attributes = $POST_DATA;
            if ($model->save()) {
                if (@$_REQUEST['backPage']) {
                    return $this->redirect($_REQUEST['backPage']);
                }
                if ($isNewRecord) {
                    return $this->redirect(['ui-update', 'id' => $model->id]);
                } else {
                    return $this->refresh();
                }
            }
        }
        $this->layout = 'admin';

        $args = ['model' => $model];

        return $this->render('ui-update', $args);
    }

    private function getConfigClass($table = 'Config')
    {
        if ($table != 'Config') {
            $table = $_GET['table'];
            $class = ("romanzaytsev\\cms\\models\\" . $table);
        } else {
            $class = Config::className();
        }
        return $class;
    }
}
