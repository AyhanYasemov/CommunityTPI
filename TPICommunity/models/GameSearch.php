<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Games;

/**
 * GameSearch represents the model behind the search form of `app\models\Games`.
 */
class GameSearch extends Games
{

    public $fkGenre_id;
    public $fkPlatform_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_game'], 'integer'],
            [['release_date', 'name'], 'safe'],
            ['fkGenre_id', 'integer'],
            ['fkPlatform_id', 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Games::find()->alias('g')
            ->joinWith(['genres ge', 'platforms pl']);

        $dataProvider = new ActiveDataProvider(['query' => $query]);

        // charger les params
        $this->load($params);

        // filtres
        $query->andFilterWhere(['g.id_game'       => $this->id_game])
            ->andFilterWhere(['ge.id_genre'     => $this->fkGenre_id])
            ->andFilterWhere(['pl.id_platform'  => $this->fkPlatform_id])
            ->andFilterWhere(['like', 'g.name',  $this->name]);

        return $dataProvider;
    }
}
