<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\Games;
use app\models\Genre;
use app\models\Platform;

$this->title = "Mes sessions";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="session-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Créer une session', ['create'], ['class'=>'btn btn-success']) ?>
        <?= Html::a('Rejoindre une session', ['join'],   ['class'=>'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class'=>'yii\grid\SerialColumn'],

            'name',
            [
                'label'=>'Jeu',
                'value'=>function($m){ return $m->game->name; },
            ],
            [
                'label'=>'Genres',
                'format'=>'raw',
                'value'=>function($m){
                    $names = ArrayHelper::getColumn($m->game->genres,'name');
                    return implode(', ',$names);
                },
            ],
            [
                'label'  => 'Plateformes',
                'format' => 'raw',
                'value'  => function($m) {
                    // Avant : $m->game->platforms
                    // Maintenant : on affiche celles que l’hôte a sélectionnées
                    $names = ArrayHelper::getColumn($m->platforms, 'name');
                    return empty($names)
                        ? '<span class="text-muted">Aucune</span>'
                        : Html::encode(implode(', ', $names));
                },
            ],
            
            'start_date:datetime',
            'end_date:datetime',

            [
                'class'=>'yii\grid\ActionColumn',
                'template'=>'{update} {cancel}',
                'buttons'=>[
                    'update'=>function($url,$model){
                        if ($model->FKid_host==Yii::$app->user->id) {
                            return Html::a('Modifier',['update','id'=>$model->id_session],['class'=>'btn btn-sm btn-info']);
                        }
                        return '';
                    },
                    'cancel'=>function($url,$model){
                        $isHost = $model->FKid_host==Yii::$app->user->id;
                        $label  = $isHost
                            ? 'Annuler session'
                            : 'Annuler participation';
                        return Html::a(
                            $label,
                            ['cancel','id'=>$model->id_session],
                            [
                                'class'=>'btn btn-sm btn-danger',
                                'data'=>[
                                    'confirm'=> $isHost
                                        ? 'Supprimer cette session et toutes les participations ?'
                                        : 'Voulez-vous vraiment vous désinscrire de cette session ?',
                                    'method'=>'post'
                                ]
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>

</div>
