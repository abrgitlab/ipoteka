<?php
/**
 * Created by PHPStorm.
 * User: daemon
 * Date: 28.03.17
 * Time: 23:23
 */

namespace app\models;


use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class Variant
 * @package app\models
 *
 * @property integer $id
 * @property float $sum
 * @property float $percent
 * @property integer $start_date
 * @property integer $period
 * @property integer $created_at
 * @property integer $updated_at
 */

class Variant extends ActiveRecord
{

    public static function tableName()
    {
        return 'variant';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            [['sum', 'percent', 'start_date', 'period'], 'required'],
            [['sum'], 'double'],
            [['created_at', 'updated_at'], 'date'],
            [['start_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function getPayments() {
        return $this->hasMany(Payment::className(), ['variant_id' => 'id']);
    }

    public function calculate() {
        $result = [];

        $payments = $this->getPayments()->orderBy('date')->all();

        $currentDate = strtotime($this->start_date);
        $lastBasicSum = $this->sum;
        $sumLeft = $this->sum;

        for ($i = 0; $i < $this->period; ++$i) {

            $sumAnnuity = round($lastBasicSum * ($this->percent / 1200) / (1 - pow(1 + ($this->percent / 1200),-180)), 2);

            $sumPercentPart = [];
            $sumPercentPart[] = ['daysInPeriod' => date('t', $currentDate) - date('d', $currentDate), 'daysInYear' => date('z', mktime(0, 0, 0, 12, 31, date('Y', $currentDate))) + 1];
            $currentDate = strtotime('+1 month', $currentDate);
            $sumPercentPart[] = ['daysInPeriod' => date('d', $currentDate), 'daysInYear' => date('z', mktime(0, 0, 0, 12, 31, date('Y', $currentDate))) + 1];

            if ($sumPercentPart[0]['daysInYear'] == $sumPercentPart[1]['daysInYear'])
                $sumPercent = round($sumLeft * ($this->percent * ($sumPercentPart[0]['daysInPeriod'] + $sumPercentPart[1]['daysInPeriod']) / ($sumPercentPart[0]['daysInYear'] * 100)), 2);
            else
                $sumPercent = round($sumLeft * ($this->percent * $sumPercentPart[0]['daysInPeriod'] / ($sumPercentPart[0]['daysInYear'] * 100)), 2) + round($sumLeft * ($this->percent * $sumPercentPart[1]['daysInPeriod'] / ($sumPercentPart[1]['daysInYear'] * 100)), 2);

            if ($sumAnnuity > $sumLeft)
                $sumAnnuity = $sumLeft + $sumPercent;

            $sumDebt = round($sumAnnuity - $sumPercent, 2);
            $sumLeft = round($sumLeft - $sumDebt, 2);

            $paymentDate = $currentDate;
            if (date('N', $currentDate) == 6)
                $paymentDate = strtotime('+2 days', $currentDate);
            elseif (date('N', $currentDate) == 7)
                $paymentDate = strtotime('+1 day', $currentDate);

            $result[] = [
                'date' => date('Y-m-d', $paymentDate),
                'sum_debt' => $sumDebt,
                'sum_percent' => $sumPercent,
                'sum_annuity' => $sumAnnuity,
                'sum_left' => $sumLeft
            ];
        }

        return $result;
    }

}