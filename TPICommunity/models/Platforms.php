<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "platforms".
 *
 * @property int $id Primary Key
 * @property string|null $name
 *
 * @property Games[] $games
 */
class Platforms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'platform';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_platform' => 'ID platform',
            'name' => 'Platform Name',
        ];
    }

    /**
     * Gets query for [[Games]].
     * Using the `game_platforms` intermediate table.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGames()
    {
        return $this->hasMany(Games::class, ['id_game' => 'FKid_game'])
                    ->viaTable('game_platform', ['FKid_platform' => 'id_platform']);
    }
}
?>