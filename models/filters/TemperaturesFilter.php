<?php

namespace app\models\filters;

use app\models\Temperatures;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\validators\DateValidator;

class TemperaturesFilter extends \app\models\Temperatures
{
    // add the public attributes that will be used to store the data to be search
    public $sid;
    public $vahemik;
    public $precision = 1;
    public $startTime;
    public $endTime;

    // now set the rules to make those attributes safe
    public function rules()
    {
        return [
            [['sid', 'vahemik'], 'string'], // Validaator
            [['precision'], 'integer'],
            [['startTime', 'endTime'], 'date', 'type' => DateValidator::TYPE_DATETIME, 'format' => 'php:Y-m-d H:i', 'message' => 'Midagi on katki'] // datetime validaator
        ];
    }

    public function search($params)
    {
        // create ActiveQuery
        $query = \app\models\Temperatures::find();
        $this->load($params, '');
        $this->load($params);
        if (!$this->validate()) { // Validaator.
            return $query;
        }
        $query->andFilterWhere(['sid' => $this->sid]);
        $query->andFilterWhere(['>', 'time', $this->startTime]);
        $query->andFilterWhere(['<', 'time', $this->endTime]);

        if ($this->vahemik) {
            if ($this->vahemik == 'tana') {
                $start = 'CURDATE()';
                $end = 'NOW()';
            } elseif ($this->vahemik == 'eile') {
                $start = 'DATE_SUB(CURDATE(), INTERVAL 1 DAY) ';
                $end = 'ADDTIME(DATE_SUB(CURDATE(), INTERVAL 1 DAY), "23:59:59")';
            } elseif ($this->vahemik == 'nadal') {
                $start = 'DATE_SUB(CURDATE(), INTERVAL(WEEKDAY(CURDATE())) DAY)';
                $end = 'NOW()';
            } else {
                $start = 'DATE_FORMAT(NOW() ,\'%Y-%m-01\')';
                $end = 'NOW()';
            }
            $query->andWhere(['between', 'time', new Expression($start), new Expression($end)]);
        }



        return $query;
    }

}