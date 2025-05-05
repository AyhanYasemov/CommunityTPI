<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
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
    
        $model = new \app\models\LoginForm();
    
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // l’utilisateur est authentifié avec succès :
                $identity = Yii::$app->user->identity;
        
    
            return $this->goHome();
        }
    
        return $this->render('login', [
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
    // Met à jour le statut avant de déconnecter
    if (!Yii::$app->user->isGuest) {
        $user = Yii::$app->user->identity;
        // On force une date ancienne pour que computedStatus renvoie 1 (déconnecté)
        $user->last_activity = (new \DateTime('-1 hour'))->format('Y-m-d H:i:s');
        $user->save(false);
    }

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
