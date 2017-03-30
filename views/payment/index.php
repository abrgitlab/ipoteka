<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $variant_id int */

$this->title = 'Досрочные платежи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Назад', ['site/view', 'id' => $variant_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Новый платёж', ['create', 'variant_id' => $variant_id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'date',
            'sum',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'delete' => function($url, $model, $key) {
                        $options = [
                            'title' => 'Удалить',
                            'aria-label' => 'Удалить',
                            'data-pjax' => '0',
                            'data-confirm' => 'Вы действительно хотите удалить данный платёж?',
                            'data-method' => 'post',
                        ];
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]);
                        return Html::a($icon, Url::to(['payment/delete', 'id' => $model->id, 'variant_id' => $model->variant_id]), $options);
                    },
                    'update' => function($url, $model, $key) {
                        $options = [
                            'title' => 'Изменить',
                            'aria-label' => 'Изменить',
                            'data-pjax' => '0',
                        ];
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-pencil"]);
                        return Html::a($icon, Url::to(['payment/update', 'id' => $model->id, 'variant_id' => $model->variant_id]), $options);
                    }
                ]
            ],
        ],
    ]); ?>
</div>
