<?php

namespace app\controllers;

use app\models\Participate;
use app\models\ParticipateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ParticipateController implements the CRUD actions for Participate model.
 */
class ParticipateController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Participate models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ParticipateSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Participate model.
     * @param int $FKid_user F Kid User
     * @param int $FKid_session F Kid Session
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($FKid_user, $FKid_session)
    {
        return $this->render('view', [
            'model' => $this->findModel($FKid_user, $FKid_session),
        ]);
    }

    /**
     * Creates a new Participate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Participate();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'FKid_user' => $model->FKid_user, 'FKid_session' => $model->FKid_session]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Participate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $FKid_user F Kid User
     * @param int $FKid_session F Kid Session
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($FKid_user, $FKid_session)
    {
        $model = $this->findModel($FKid_user, $FKid_session);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'FKid_user' => $model->FKid_user, 'FKid_session' => $model->FKid_session]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Participate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $FKid_user F Kid User
     * @param int $FKid_session F Kid Session
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($FKid_user, $FKid_session)
    {
        $this->findModel($FKid_user, $FKid_session)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Participate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $FKid_user F Kid User
     * @param int $FKid_session F Kid Session
     * @return Participate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($FKid_user, $FKid_session)
    {
        if (($model = Participate::findOne(['FKid_user' => $FKid_user, 'FKid_session' => $FKid_session])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
