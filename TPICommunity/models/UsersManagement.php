<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id Primary Key
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string|null $birthDate
 * @property string $creationDate
 * @property string $lastUpdated
 * @property string|null $discordFriendLink
 *
 * @property Availability $availability
 */
class UsersManagement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password', 'creationDate', 'lastUpdated'], 'required'],
            [['birthDate', 'creationDate', 'lastUpdated'], 'safe'],
            [['username', 'email', 'password', 'discordFriendLink'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'birthDate' => 'Birth Date',
            'creationDate' => 'Creation Date',
            'lastUpdated' => 'Last Updated',
            'discordFriendLink' => 'Discord Friend Link',
        ];
    }

    /**
     * Gets query for [[Availability]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAvailability()
    {
        return $this->hasOne(Availability::class, ['id' => 'id']);
    }
}
