<?php

namespace romanzaytsev\cms\controllers;

use romanzaytsev\cms\models\Config;
use romanzaytsev\cms\models\Ui;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class FileController extends Controller
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

    public function actionUpload()
    {
        $uploaddir = "/uploads/" . @$_REQUEST['dir'];
        $uploaddirAbsolute = \Yii::getAlias('@webroot') . $uploaddir;

        $filename = Yii::$app->MyTools::makeFileUrl($_FILES['file']['name']);
        $filenameExt = "";
        $dotpos = strripos($filename, ".");
        if ($dotpos >= 0) {
            $filenameExt = substr($filename, $dotpos);
            $filename = substr($filename, 0, $dotpos);
        }
        $savingName = $filename . $filenameExt;
        $imagepath = $uploaddirAbsolute . $savingName;
        $index = 0;
        while (true) {
            $imagepath = $uploaddirAbsolute . $savingName;
            if (file_exists($imagepath)) {
                ++$index;
                $savingName = $filename . $index . $filenameExt;
            } else {
                break;
            }
        }

        if (!file_exists($uploaddirAbsolute)) {
            mkdir($uploaddirAbsolute, 0777, true);
        }

        $filelink = $uploaddir . $savingName;
        $uploadfile = $uploaddirAbsolute . basename($savingName);

        $result = [];
        if (\Yii::$app->request->isPost) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
                $result['filelink'] = $filelink;
                $result['filename'] = $savingName;
            } else {
                $result['error'] = "Possible file upload attack!";
            }
        }
        echo json_encode($result);
        exit;
    }
}
