<?php
namespace app\components;

use yii\base\Behavior;
use yii\web\Application;
use yii\db\Expression;

class LastActivityBehavior extends Behavior
{
    public function events()
    {
        return [
            Application::EVENT_BEFORE_REQUEST => 'updateLastActivity',
        ];
    }

    public function updateLastActivity()
    {
        if (!\Yii::$app->user->isGuest) {
            $user = \Yii::$app->user->identity;
            $user->last_activity = new Expression('NOW()');
            $user->save(false, ['last_activity']);
        }
    }
}
