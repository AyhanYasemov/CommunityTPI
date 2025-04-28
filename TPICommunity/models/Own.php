<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "OWN".
 *
 * @property int $FKid_user
 * @property int $FKid_game
 *
 * @property Game $game
 * @property User $user
 */
class Own extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'OWN';  
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['FKid_user', 'FKid_game'], 'required'],
            [['FKid_user', 'FKid_game'], 'integer'],
            [['FKid_user', 'FKid_game'], 'unique', 'targetAttribute' => ['FKid_user', 'FKid_game']],
            [['FKid_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['FKid_user' => 'id_user']],  // Correction de la relation User
            [['FKid_game'], 'exist', 'skipOnError' => true, 'targetClass' => Games::class, 'targetAttribute' => ['FKid_game' => 'id_game']],  // Correction de la relation Game
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'FKid_user' => 'User ID',
            'FKid_game' => 'Game ID',
        ];
    }

    /**
     * Gets query for [[Game]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGame()
    {
        return $this->hasOne(Games::class, ['id_game' => 'FKid_game']);  // Correction de la clé primaire de Game
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id_user' => 'FKid_user']);  // Correction de la clé primaire de User
    }
}
