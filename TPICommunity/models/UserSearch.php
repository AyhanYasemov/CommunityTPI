<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

class UserSearch extends User
{
    /** Filtres pour genres et plateformes */
    public $genreFilter = [];
    public $platformFilter = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return  [
            [['username'], 'safe'], 
            ['genreFilter', 'each', 'rule' => ['integer']],
            ['platformFilter', 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // On bypass les scenarios du parent
        return Model::scenarios();
    }

    /**
     * Recherche avec possibilité de filtrer par genre et plateforme.
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find()->orderBy('username');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        // Charge les paramètres (GET)
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        // Si un ou plusieurs genres sont sélectionnés, on joint la table genre
        if ($this->genreFilter) {
            $query->joinWith('preferredGenres')
                  ->andWhere(['genre.id_genre' => $this->genreFilter]);
        }

        // Pareil pour les plateformes
        if ($this->platformFilter) {
            $query->joinWith('preferredPlatforms')
                  ->andWhere(['platform.id_platform' => $this->platformFilter]);
        }

        $query->andFilterWhere(['like', 'username', $this->username]);


        return $dataProvider;
    }
}
