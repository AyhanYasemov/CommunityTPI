<?php

namespace app\controllers;

use Yii;
use app\models\Session;
use app\models\SessionsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;

/**
 * SessionsController implements the CRUD actions for Sessions model.
 */
class SessionsController extends Controller
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
     * Lists all Sessions models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SessionsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sessions model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Sessions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Session();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Sessions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Sessions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Sessions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Sessions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Session::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionCreateSession()
    {
        return $this->render('create-session');
    }

    public function actionModifySession()
    {
        return $this->render('modify-session');
    }

    public function actionViewSession()
    {
        return $this->render('view-session');
    }

    public function actionAddUsers($sessionId)
{
    $session = $this->findModel($sessionId);
    
    // L'hôte est celui qui crée la session
    $hostId = Yii::$app->user->id;

    // Vérifier si l'utilisateur est l'hôte de la session
    if ($session->user_id != $hostId) {
        throw new ForbiddenHttpException("You are not allowed to add users to this session.");
    }

    $usersToAdd = Yii::$app->request->post('user', []); // Récupérer les utilisateurs ajoutés

    foreach ($usersToAdd as $userId) {
        // Vérifier si l'utilisateur a le jeu et est disponible pour cette session
        if ($this->checkUserAvailability($userId, $session)) {
            // Logique pour ajouter l'utilisateur à la session (par exemple, créer une entrée dans une table pivot)
            // Par exemple, un modèle "SessionUsers" qui contient `session_id` et `user_id`
            $this->addUserToSession($userId, $sessionId);
        } else {
            // Vous pouvez gérer ici l'erreur si l'utilisateur ne peut pas être ajouté à la session
        }
    }

    return $this->redirect(['view', 'id' => $sessionId]);
}

protected function checkUserAvailability($userId, $session)
{
    // Vérification de la disponibilité de l'utilisateur
    // Vérifier si l'utilisateur est disponible pendant la session et s'il possède le jeu
    return true; // Cette logique devrait être développée en fonction des règles d'affaires
}

protected function addUserToSession($userId, $sessionId)
{
    // Logique d'ajout de l'utilisateur à la session (par exemple, dans une table pivot)
    // Par exemple :
    Yii::$app->db->createCommand()->insert('session_users', [
        'session_id' => $sessionId,
        'user_id' => $userId,
    ])->execute();
}


}
