<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "preference".
 *
 * @property int $id_preference
 * @property int $FKid_user
 * @property int|null $FKid_game
 * @property int|null $FKid_genre
 * @property int $level
 *
 * @property Game $game
 * @property Genre $genre
 * @property User $user
 */
class Preference extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'preference';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['FKid_game', 'FKid_genre'], 'default', 'value' => null],
            [['FKid_user', 'level'], 'required'],
            [['FKid_user', 'FKid_game', 'FKid_genre', 'level'], 'integer'],
            [['FKid_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['FKid_user' => 'id_user']],
            [['FKid_game'], 'exist', 'skipOnError' => true, 'targetClass' => Games::class, 'targetAttribute' => ['FKid_game' => 'id_game']],
            [['FKid_genre'], 'exist', 'skipOnError' => true, 'targetClass' => Genres::class, 'targetAttribute' => ['FKid_genre' => 'id_genre']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_preference' => 'Id Preference',
            'FKid_user' => 'F Kid User',
            'FKid_game' => 'F Kid Game',
            'FKid_genre' => 'F Kid Genre',
            'level' => 'Level',
        ];
    }

    /**
     * Gets query for [[Game]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGame()
    {
        return $this->hasOne(Games::class, ['id_game' => 'FKid_game']);
    }

    /**
     * Gets query for [[Genre]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGenre()
    {
        return $this->hasOne(Genres::class, ['id_genre' => 'FKid_genre']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id_user' => 'FKid_user']);
    }

}
