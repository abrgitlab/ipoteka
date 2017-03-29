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

        $period = $this->period;

        for ($i = 0; $i < $this->period; ++$i) {
            $nextDate = strtotime('+1 month', $currentDate);

            $daysInPeriodLeft = date('t', $currentDate);

            $lastFastPayment = 0;
            foreach ($payments as $payment) {
                $paymentDate = strtotime($payment->date);
                $daysInPeriod = 0;
                if ($paymentDate > $currentDate && $paymentDate < $nextDate) {
                    $daysInPeriod = date('d', $paymentDate - $currentDate) - 1;
                    $sumPercent = round($sumLeft * ($this->percent * $daysInPeriod / ((date('z', mktime(0, 0, 0, 12, 31, date('Y', $currentDate))) + 1) * 100)), 2);
                    $sumDebt = round($payment->sum - $sumPercent, 2);
                    $sumLeft = round($sumLeft - $sumDebt, 2);
                    $result[] = [
                        'date' => date('Y-m-d', $paymentDate),
                        'days_in_period' => 0 + $daysInPeriod,
                        'sum_debt' => $sumDebt,
                        'sum_percent' => $sumPercent,
                        'sum_annuity' => $payment->sum,
                        'sum_left' => $sumLeft
                    ];

                    $lastFastPayment += $sumDebt;
                    $lastBasicSum = $sumLeft;
                    $dateDiff = $currentDate - strtotime($this->start_date);
                    $period = $this->period - date('m', $dateDiff) + 12 * (date('Y', $dateDiff) - 1970);
                }

                $daysInPeriodLeft -= $daysInPeriod;
            }

            $sumPercentPart = [];
            $daysInPeriodLeft -= date('d', $currentDate);
            $sumPercentPart[] = ['daysInPeriod' => max($daysInPeriodLeft, 0), 'daysInYear' => date('z', mktime(0, 0, 0, 12, 31, date('Y', $currentDate))) + 1];
            $sumPercentPart[] = ['daysInPeriod' => date('d', $nextDate) + min($daysInPeriodLeft, 0), 0, 'daysInYear' => date('z', mktime(0, 0, 0, 12, 31, date('Y', $nextDate))) + 1];

            if ($sumPercentPart[0]['daysInYear'] == $sumPercentPart[1]['daysInYear'])
                $sumPercent = round($sumLeft * ($this->percent * ($sumPercentPart[0]['daysInPeriod'] + $sumPercentPart[1]['daysInPeriod']) / ($sumPercentPart[0]['daysInYear'] * 100)), 2);
            else
                $sumPercent = round($sumLeft * ($this->percent * $sumPercentPart[0]['daysInPeriod'] / ($sumPercentPart[0]['daysInYear'] * 100)), 2) + round($sumLeft * ($this->percent * $sumPercentPart[1]['daysInPeriod'] / ($sumPercentPart[1]['daysInYear'] * 100)), 2);

            $sumAnnuity = round($lastBasicSum * ($this->percent / 1200) / (1 - pow(1 + ($this->percent / 1200), -$period)), 2);
            $sumAnnuity = max($sumAnnuity - $lastFastPayment, $sumPercent);

            if (($sumAnnuity > $sumLeft) || ($i == ($this->period - 1)) && ($sumAnnuity < $sumLeft))
                $sumAnnuity = $sumLeft + $sumPercent;

            $sumDebt = round($sumAnnuity - $sumPercent, 2);
            $sumLeft = round($sumLeft - $sumDebt, 2);

            $paymentDate = $nextDate;
            if (date('N', $nextDate) == 6)
                $paymentDate = strtotime('+2 days', $nextDate);
            elseif (date('N', $nextDate) == 7)
                $paymentDate = strtotime('+1 day', $nextDate);

            $result[] = [
                'date' => date('Y-m-d', $paymentDate),
                'days_in_period' => 0,
                'sum_debt' => $sumDebt,
                'sum_percent' => $sumPercent,
                'sum_annuity' => $sumAnnuity,
                'sum_left' => $sumLeft
            ];

            $currentDate = $nextDate;
        }

        return $result;
    }

}