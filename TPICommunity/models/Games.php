<?php

namespace app\models;
use yii\helpers\ArrayHelper;

use Yii;

/**
 * This is the model class for table "GAME".
 *
 * @property int $id_game Primary Key
 * @property string|null $release_date
 * @property string $name
 *
 * @property Genres[] $genres
 * @property Platforms[] $platforms
 * @property Sessions[] $sessions
 * @property User[] $usersWhoOwn
 */
class Games extends \yii\db\ActiveRecord
{
    /** pour stocker les genres cochés dans le formulaire */
    public $fkGenre_id = [];

    /** pour stocker les plateformes cochées dans le formulaire */
    public $fkPlatform_id = [];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'game'; // Assurez-vous que le nom de la table est bien 'game'
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['release_date', 'safe'],
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
    
            // nos nouveaux attributs virtuels :
            ['fkGenre_id', 'each', 'rule' => ['integer']],
            ['fkPlatform_id', 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_game' => 'ID',
            'name' => 'Game Name',
            'release_date' => 'Release Date',
        ];
    }

    /**
     * Gets query for the associated genres.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGenres()
    {
        return $this->hasMany(Genres::class, ['id_genre' => 'FKid_genre'])
            ->viaTable('game_genre', ['FKid_game' => 'id_game']);
    }

    /**
     * Gets query for the associated platforms.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlatforms()
    {
        return $this->hasMany(Platforms::class, ['id_platform' => 'FKid_platform'])
            ->viaTable('game_platform', ['FKid_game' => 'id_game']);
    }

    /**
     * Gets query for [[Sessions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSessions()
    {
        return $this->hasMany(Session::class, ['FKid_game' => 'id_game']);
    }

    /**
     * Gets query for users who own this game.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsersWhoOwn()
    {
        return $this->hasMany(User::class, ['id_user' => 'FKid_user'])
            ->viaTable('own', ['FKid_game' => 'id_game']);
    }

    /**
     * Gets query for the associated User who owns the game.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id_user' => 'FKid_user']);
    }

    public function afterFind()
{
    parent::afterFind();
    $this->fkGenre_id     = ArrayHelper::getColumn($this->genres,    'id_genre');
    $this->fkPlatform_id  = ArrayHelper::getColumn($this->platforms, 'id_platform');
}

public function afterSave($insert, $changedAttributes)
{
    parent::afterSave($insert, $changedAttributes);

    // -- genres pivot --
    \app\models\GameGenre::deleteAll(['FKid_game' => $this->id_game]);
    foreach ($this->fkGenre_id as $gId) {
        $pivot = new \app\models\GameGenre();
        $pivot->FKid_game  = $this->id_game;
        $pivot->FKid_genre = $gId;
        $pivot->save(false);
    }

    // -- plateformes pivot --
    \app\models\GamePlatform::deleteAll(['FKid_game' => $this->id_game]);
    foreach ($this->fkPlatform_id as $pId) {
        $pivot = new \app\models\GamePlatform();
        $pivot->FKid_game      = $this->id_game;
        $pivot->FKid_platform  = $pId;
        $pivot->save(false);
    }
}


}

