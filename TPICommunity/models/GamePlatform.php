<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gameplayableplatforms".
 *
 * @property int $game_id
 * @property int $platform_id
 *
 * @property Games $game
 * @property Platforms $platform
 */
class GamePlatform extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'game_platform';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['game_id', 'platform_id'], 'required'],
            [['game_id', 'platform_id'], 'integer'],
            [['game_id', 'platform_id'], 'unique', 'targetAttribute' => ['game_id', 'platform_id']],
            [['game_id'], 'exist', 'skipOnError' => true, 'targetClass' => Games::class, 'targetAttribute' => ['game_id' => 'id']],
            [['platform_id'], 'exist', 'skipOnError' => true, 'targetClass' => Platforms::class, 'targetAttribute' => ['platform_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'game_id' => 'Jeu ID',
            'platform_id' => 'Plateforme ID',
        ];
    }

    /**
     * Gets query for [[Game]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGame()
    {
        return $this->hasOne(Games::class, ['id' => 'game_id']);
    }

    /**
     * Gets query for [[Platform]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlatform()
    {
        return $this->hasOne(Platforms::class, ['id' => 'platform_id']);
    }
}
