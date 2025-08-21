<?php

use app\modules\process\widgets\Select2Template;
use kartik\widgets\ActiveForm;
use yii\bootstrap4\LinkPager;
use yii\helpers\Html;
use app\widgets\DateRangePickerWithRanges;

/* @var $this yii\web\View */
/* @var $model app\modules\process\models\FormReq3SearchArchive */
/* @var $tasks app\modules\process\models\task_archive\TaskArchive[] */
/* @var $pager yii\data\Pagination */

$this->title = 'Архив задач';
?>

<div class="task-archive-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="task-archive-search mb-3">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
        ]); ?>

        <?= $form->field($model, 'template_id')->widget(Select2Template::class) ?>
        <?= $form->field($model, 'template_name') ?>
        <?= $form->field($model, 'dateRange', [
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

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Шаблон</th>
            <th>Дата создания</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= Html::encode($task->task_id) ?></td>
                <td><?= Html::encode($task->task_name) ?></td>
                <td><?= Html::encode($task->template_name) ?></td>
                <td><?= Html::encode($task->task_date_create) ?></td>
                <td><?= Html::a('Просмотр', ['view', 'id' => $task->task_id]) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?= LinkPager::widget(['pagination' => $pager]); ?>
</div>
