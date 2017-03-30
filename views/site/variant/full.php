<?php
/**
 * Created by PhpStorm.
 * User: daemon
 * Date: 29.03.17
 * Time: 15:18
 */
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model \app\models\Variant */
/* @var $dataProvider \yii\data\ArrayDataProvider */

$this->title = 'График #' . $model->id;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'date',
        //'days_in_period',
        'sum_debt',
        'sum_percent',
        'sum_annuity',
        'sum_left',
    ],
]);
?>
<p><strong>Всего по основному долгу: </strong> <?= $model->sumDebtFull ?> (выплачено: <?= $model->sumDebtPayed ?>)</p>
<p><strong>Всего по процентам: </strong> <?= $model->sumPercentFull ?> (выплачено: <?= $model->sumPercentPayed ?>)</p>
