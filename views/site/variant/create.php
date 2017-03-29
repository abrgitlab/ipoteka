<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Variant */

$this->title = 'Create Variant';
$this->params['breadcrumbs'][] = ['label' => 'Variants', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="variant-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
