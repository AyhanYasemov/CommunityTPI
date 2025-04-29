<?php

namespace app\controllers;

use app\models\Session;
use app\models\SessionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SessionController implements the CRUD actions for Session model.
 */
class SessionController extends Controller
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
     * Lists all Session models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SessionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Session model.
     * @param int $id_session Id Session
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id_session)
    {
        return $this->render('view', [
            'model' => $this->findModel($id_session),
        ]);
    }

    /**
     * Creates a new Session model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Session();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id_session' => $model->id_session]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Session model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id_session Id Session
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id_session)
    {
        $model = $this->findModel($id_session);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id_session' => $model->id_session]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Session model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id_session Id Session
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id_session)
    {
        $this->findModel($id_session)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Session model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id_session Id Session
     * @return Session the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_session)
    {
        if (($model = Session::findOne(['id_session' => $id_session])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
