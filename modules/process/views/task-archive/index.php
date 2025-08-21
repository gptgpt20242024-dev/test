<?php

use app\modules\process\widgets\Select2Template;
use app\widgets\DateRangePickerWithRanges;
use kartik\widgets\ActiveForm;
use yii\bootstrap4\LinkPager;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\process\models\FormReq3SearchArchive */
/* @var $tasks app\modules\process\models\task_archive\TaskArchive[] */
/* @var $pager yii\data\Pagination */

$this->title = 'Архив задач';
?>

<div class="task-archive-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card mb-3">
        <div class="card-body">
            <div class="task-archive-search">
                <?php $form = ActiveForm::begin([
                    'method' => 'get',
                ]); ?>

                <?= $form->field($model, 'templateId')->widget(Select2Template::class) ?>
                <?= $form->field($model, 'templateName') ?>
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
        </div>
    </div>

    <?php foreach ($tasks as $task): ?>
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <?= Html::a(Html::encode($task->task_name), ['view', 'id' => $task->task_id]) ?>
                </h5>
                <small class="text-muted">
                    <?= Html::encode($task->template_name) ?>
                </small>
            </div>
            <div class="card-footer text-muted">
                Создана: <?= Html::encode($task->task_date_create) ?><br>
                Архивирована: <?= Html::encode($task->date_add_to_archive) ?>
            </div>
        </div>
    <?php endforeach; ?>


    <?= LinkPager::widget(['pagination' => $pager]); ?>
</div>
