<?php
/**
 * Created by PHPStorm.
 * User: daemon
 * Date: 28.03.17
 * Time: 23:38
 */

namespace app\models;


use yii\db\ActiveRecord;

/**
 * Class Payments
 * @package app\models
 *
 * @property integer $id
 * @property integer $variant_id
 * @property integer $date
 * @property float $sum
 */

class Payment extends ActiveRecord
{

    public static function tableName()
    {
        return 'payment';
    }

    public function rules()
    {
        return [
            [['variant_id', 'sum', 'date'], 'required'],
            [['variant_id'], 'integer'],
            [['sum'], 'double'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function getVariant() {
        return $this->hasOne(Variant::className(), ['id', 'variant_id']);
    }

}