<?php

use app\modules\process\widgets\Select2Template;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;
use app\widgets\DateRangePickerWithRanges;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\process\models\FormReq3SearchArchive */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Архив задач';
?>

<div class="task-archive-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="task-archive-search mb-3">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
        ]); ?>

        <?= $form->field($searchModel, 'template_id')->widget(Select2Template::class) ?>
        <?= $form->field($searchModel, 'template_name') ?>
        <?= $form->field($searchModel, 'dateRange', [
            'addon'   => ['prepend' => ['content' => '<i class="fas fa-calendar-alt"></i>']],
            'options' => ['class' => 'drp-container form-group']
        ])->widget(DateRangePickerWithRanges::class, [
            'unsetRanges' => ['Сегодня'],
            'options'        => ['class' => 'form-control', 'autocomplete' => 'off'],
            'useWithAddon'   => true,
            'presetDropdown' => false,
            'convertFormat'  => true,
        ]); ?>

        <div class="form-group">
            <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'task_id',
            'task_name',
            'template_name',
            'task_date_create',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'urlCreator' => function ($action, $model) {
                    return ['view', 'id' => $model->task_id];
                }
            ],
        ],
    ]); ?>
</div>
