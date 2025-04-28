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
            'id_platform' => 'ID', // Modifie ici pour 'id_platform'
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
        return $this->hasMany(Games::class, ['id' => 'game_id'])
                    ->viaTable('game_platform', ['platform_id' => 'id']);
    }
}
?>