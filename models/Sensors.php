<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sensors".
 *
 * @property int $id
 * @property int $uid
 * @property string $sid
 * @property string $name
 * @property string $time
 */
class Sensors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sensors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'uid', 'sid', 'name'], 'required'],
            [['id', 'uid'], 'integer'],
            [['time'], 'safe'],
            [['sid'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'sid' => 'Sid',
            'name' => 'Name',
            'time' => 'Time',
        ];
    }
}
