<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Графики';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="variant-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Новый график', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'sum',
            'percent',
            'start_date',
            'period',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {clone}',
                'buttons' => [
                    'clone' => function($url, $model, $key) {
                        $options = [
                            'title' => 'Клонировать',
                            'aria-label' => 'Клонировать',
                            'data-pjax' => '0',
                        ];
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-copy"]);
                        return Html::a($icon, Url::to(['clone', 'id' => $model->id]), $options);
                    }
                ]
            ],
        ],
    ]); ?>
</div>
