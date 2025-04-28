<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    /**
     * {@inheritdoc}
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $request = Yii::$app->request->post();

        $user = new User();
        if($request)
        {
            if ($user->load($request) && $user->login())
            {
                return $this->redirect(["site/index"]);
            }

            $session = Yii::$app->session;
            $session->setFlash('errorMessages', $user->getErrors());
        }


        $user->password = '';
        return $this->render('login', [
            'user' => $user,
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

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signup action.
     *
     * @return Response|string
     */
    public function actionSignup()
    {
        $user = new User();
        $session = Yii::$app->session;
    
        if ($user->load(Yii::$app->request->post())) {
            // Affectation des données et hachage du mot de passe
            $postData = Yii::$app->request->post('User', []);
            $user->username = $postData['username'] ?? null;
            $user->password = Yii::$app->getSecurity()->generatePasswordHash($user->password);
    
            if ($user->validate() && $user->save()) {
                $session->setFlash('successMessage', 'Inscription Réussie');
                return $this->redirect(['site/login']);
            } else {
                $session->setFlash('errorMessages', $user->getErrors());
            }
        }
    
        return $this->render('signup', [
            'model' => $user,
        ]);
    }
}
