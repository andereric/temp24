<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "temperatures".
 *
 * @property int $id
 * @property int $uid
 * @property string $sid
 * @property float $temperature
 * @property string $time
 */
class Temperatures extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'temperatures';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'sid', 'temperature'], 'required'],
            [['uid'], 'integer'],
            [['temperature'], 'number'],
            [['time'], 'safe'],
            [['sid'], 'string', 'max' => 20],
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
            'temperature' => 'Temperature',
            'time' => 'Time',
        ];
    }

    public function getTemperatureTime()
    {
        return $this->hasOne(Temperatures::find(), ['id' => 'time']);
    }


}
