<?php

namespace romanzaytsev\cms\controllers;

use romanzaytsev\cms\models\ChangePasswordForm;
use romanzaytsev\cms\models\LoginForm;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

class AuthController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
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

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['page/list']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if (Yii::$app->user->identity->isAdmin()) {
                return $this->redirect(['page/list']);
            } else {
                return $this->goBack();
            }
        }
        $this->layout = 'admin';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionChangePassword()
    {
        $id = \Yii::$app->user->id;
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/login']);
        }

        try {
            $model = new ChangePasswordForm($id);
        } catch (InvalidParamException $e) {
            throw new \yii\web\BadRequestHttpException($e->getMessage());
        }

        if ($model->load(\Yii::$app->request->post()) && $model->validate() && $model->changePassword()) {
            \Yii::$app->session->setFlash('success', 'Password Changed!');
            return $this->redirect(['auth/logout']);
        }
        $this->layout = 'admin';
        return $this->render('changePassword', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(['auth/index']);
    }

    public function URLto($route)
    {
        return Url::to($route);
    }

    public function beforeAction($action)
    {
        if ($action->id == 'upload') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionUpdateCaptcha()
    {
        $session = Yii::$app->session;
        $session->open();
        $random_alpha = md5(rand());
        $captcha_code = substr($random_alpha, 0, 6);
        Yii::$app->db->createCommand()->update('session', ['captcha' => $captcha_code], ['id' => \Yii::$app->session->id])->execute();
        $_SESSION["captcha_code"] = $captcha_code;
        $target_layer = imagecreatetruecolor(70, 30);
        $captcha_background = imagecolorallocate($target_layer, 255, 160, 119);
        imagefill($target_layer, 0, 0, $captcha_background);
        $captcha_text_color = imagecolorallocate($target_layer, 0, 0, 0);
        imagestring($target_layer, 6, 8, 7, $captcha_code, $captcha_text_color);
        header("Content-type: image/jpeg");
        imagejpeg($target_layer);
        exit;
    }

    public function actionCheckCaptcha($text)
    {
        $code = @(new \yii\db\Query())
            ->select('captcha')
            ->from('session')->where(['id' => \Yii::$app->session->id])
            ->one()['captcha'];
        return $code == $text ? 'true' : 'false';
    }

    public function actionSitemap()
    {
        $list = [];
        $categories = \romanzaytsev\cms\models\PageTemplate::getAll();
        foreach ($categories as $category) {
            $pages = \romanzaytsev\cms\models\Page::find()->where(['pageTemplateId' => $category->id])->all();
            foreach ($pages as $page) {
                $list[] = $page;
            }
        }
        if($_GET['xml'] ?? false) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            Yii::$app->response->headers->add('Content-Type', 'text/xml');
            return $this->renderPartial('//site/sitemapxml', ['list' => $list]);
        } else {
            $this->layout = 'main';
            return $this->render('//site/sitemap', [
                'list' => $list,
            ]);
        }
    }
}
