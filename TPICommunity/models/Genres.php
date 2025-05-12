<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "genres".
 *
 * @property int $id Primary Key
 * @property string|null $name
 *
 * @property Games[] $games
 */
class Genres extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'genre';
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
            'id_genre' => 'ID genre',
            'name' => 'Nom du genre',
        ];
    }

    /**
 * Gets query for [[Games]] via la table pivot `game_genre`.
 *
 * @return \yii\db\ActiveQuery
 */
public function getGames()
{
    return $this->hasMany(Games::class, ['id_game' => 'FKid_game'])
                ->viaTable('game_genre', ['FKid_genre' => 'id_genre']);
}

}
?>