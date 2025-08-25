<?php

use app\components\Date;
use app\modules\process\components\HelperOper;
use app\modules\process\models\chats\Req3TasksChats;
use app\modules\process\models\FormChatSearch;
use app\modules\process\models\template\Req3Templates;
use kartik\daterange\DateRangePicker;
use kartik\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\bootstrap4\LinkPager;
use yii\data\Pagination;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $search FormChatSearch */
/* @var $chats Req3TasksChats[] */
/* @var $pager Pagination */
/* @var $onlyMy bool */
?>


<?php if (!$onlyMy): ?>
<?php $form = ActiveForm::begin(['method' => 'GET', 'action' => ['report']]); ?>
    <div class="card">
        <div class="card-header">

            <h2 class="m-0 d-inline-block" style="color: #9c3c00; font-weight: bold">Чаты (<?= $pager->totalCount ?>)</h2>

            <div class="mt-2">
                <button type="button" class="btn btn-light btn-sm" data-spoiler-btn data-status="0" data-container=".card" data-content-multi="1">Фильтр</button>
            </div>
        </div>

        <div class="card-body" data-spoiler-content style="display: none; background-color: #dddddd">


            <div class="card">
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-12">
                            <?= $form->field($search, 'templateIds')->widget(Select2::class, [
                                'data'          => Req3Templates::getSelect2List(null, false, true),
                                'options'       => ['multiple' => true, 'placeholder' => 'Выберите шаблон'],
                                'pluginOptions' => ['allowClear' => true,]
                            ]); ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($search, 'dateRange', [
                                'addon'   => ['prepend' => ['content' => '<i class="fas fa-calendar-alt"></i>']],
                                'options' => ['class' => 'drp-container form-group']
                            ])->widget(DateRangePicker::class, [
                                'options'        => ['class' => 'form-control', 'autocomplete' => 'off'],
                                'useWithAddon'   => true,
                                'presetDropdown' => false,
                                'convertFormat'  => true,
                                'pluginOptions'  => [
                                    'locale' => ['format' => 'Y-m-d'],
                                    'ranges' => [
                                        "Сегодня"            => ["moment().startOf('day')", "moment()"],
                                        "Вчера"              => ["moment().startOf('day').subtract(1,'days')", "moment().endOf('day').subtract(1,'days')"],
                                        "Последние 7 дней"   => ["moment().startOf('day').subtract(6, 'days')", "moment()"],
                                        "Последние 30 дней"  => ["moment().startOf('day').subtract(29, 'days')", "moment()"],
                                        "Последние 365 дней" => ["moment().startOf('day').subtract(364, 'days')", "moment()"],
                                        "Этот месяц"         => ["moment().startOf('month')", "moment().endOf('month')"],
                                        "Предыдущий месяц"   => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                                        "Последние 3 месяца" => ["moment().subtract(2, 'month').startOf('month')", "moment()"],
                                    ]
                                ]
                            ]); ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($search, 'closeState')->widget(Select2::class, [
                                'data'          => FormChatSearch::CLOSE_STATE_NAMES,
                                'options'       => ['placeholder' => 'Выберите статус чата'],
                                'pluginOptions' => ['allowClear' => true]
                            ]); ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($search, 'closeItemIds')->widget(Select2::class, [
                                'data'          => $search->getSelect2ListItems(),
                                'options'       => ['multiple' => true, 'placeholder' => 'Выберите причину закрытия'],
                                'pluginOptions' => ['allowClear' => true]
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer" style="display: none" data-spoiler-content>
            <?= Html::submitButton("Поиск", ['class' => "btn btn-primary"]) ?>
        </div>
    </div>
<?php ActiveForm::end() ?>
<?php endif; ?>


<?php foreach ($chats as $item): ?>
    <?php
    if ($item->is_active == 1) {
        $class = "card-primary";
    } else {
        $class = "card-success";
    }
    ?>
    <div class="card <?= $class ?> card-outline">
        <div class="card-body">

            <button type="button" class="btn btn-light float-right" onclick="showDialogChat(<?= $item->id ?>)">
                <i class="fas fa-comments" style="color: #41a2db"></i> Посмотреть
            </button>

            <div>
                <a href="<?= Url::toRoute(['/process/task/view', 'id' => $item->task_id]) ?>" target="_blank">
                    <?= $item->task->name ?? "-" ?>
                </a>
            </div>

            <div>
                <span style="color: #243a78; font-weight: bold">Тема:</span> <?= $item->topic ?>
            </div>
            <div>
                <?php $date_create = new Date($item->date_add) ?>
                <span style="color: #243a78; font-weight: bold">Создал:</span> <?= HelperOper::getFio($item, 'creator_id', 'creator') ?>
                <span style="color: #737373; font-size: small">(<?= $date_create->format(Date::FORMAT_DATE_TIME) ?>, <?= $date_create->toRemainingText(1) ?> назад.)</span>
            </div>

            <div>
                <?php $date_last_message = new Date($item->date_last_message) ?>
                <span style="color: #243a78; font-weight: bold">Дата последнего сообщения:</span> <?= $date_last_message->format(Date::FORMAT_DATE_TIME) ?><span style="color: #737373; font-size: small">, <?= $date_last_message->toRemainingText(1) ?> назад.</span>
            </div>

            <?php if (!empty($item->date_close)): ?>
                <div>
                    <?php $date_close = new Date($item->date_close) ?>
                    <span style="color: #243a78; font-weight: bold">Закрыл:</span> <?= HelperOper::getFio($item, 'close_id', 'close') ?>
                    <span style="color: #737373; font-size: small">(<?= $date_close->format(Date::FORMAT_DATE_TIME) ?>, <?= $date_close->toRemainingText(1) ?> назад.)</span>
                    <span style="color: #737373; font-size: small">(время жизни чата: <?= Date::secondsToText($date_close->subtractDateTime($date_create), 1) ?>)</span>
                </div>
                <div>
                    <?php $date_close = new Date($item->date_close) ?>
                    <span style="color: #243a78; font-weight: bold">Причина закрытия:</span> <?= $item->getCloseItemValue() ?>
                </div>
            <?php endif; ?>
            <div>
                <span style="color: #243a78; font-weight: bold">Участников:</span> <?= count($item->members) ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>


<?php if (isset($pager) && $pager != null && $pager->pageCount > 1): ?>
    <div class="card-footer">
        <?= LinkPager::widget([
            'listOptions'    => ['class' => 'pagination justify-content-center'],
            'pagination'     => $pager,
            'firstPageLabel' => 'Первая',
            'lastPageLabel'  => 'Последняя (' . $pager->pageCount . ')',
            'prevPageLabel'  => '&laquo;',
            'nextPageLabel'  => '&raquo;',
            'maxButtonCount' => 5,
        ]);
        ?>
    </div>
<?php endif; ?>