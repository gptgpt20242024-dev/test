<?php

use app\modules\process\constants\IdentifierCompleteErrors;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\template_steps\Req3TemplateStepRule2;
use app\modules\process\models\template_steps\Req3TemplateSteps;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $rule2 Req3TemplateStepRule2 */
/* @var $hasTaskCheck array */
/* @var $notCompleteIdentifiers array */
/* @var $isExceededTransitions bool */
/* @var $nextStepSetting array */

?>

<div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-start;">

    <?php if ($hasTaskCheck['step'] || $hasTaskCheck['work']): ?>
        <div class="alert alert-default-info px-2 py-1 mb-0" style="flex-basis: 1000px; flex-grow: 1;">
            <i class="fas fa-biohazard"></i> Внимание !!! Запущена
            <?php if ($hasTaskCheck['step']): ?>
                <?= Html::a("задача", ['/process/task/view', 'id' => $hasTaskCheck['step']], ['target' => '_blank', 'style' => 'color: #3c53c5;']) ?> по добавлению работы к данному шагу.
            <?php else: ?>
                <?= Html::a("задача", ['/process/task/view', 'id' => $hasTaskCheck['work']], ['target' => '_blank', 'style' => 'color: #3c53c5;']) ?> на изменение работы данного шага.
            <?php endif; ?>
            <button type="button" class="btn btn-outline-info btn-xs" data-spoiler data-container=".alert" data-content="[data-spoiler-content]" onclick="loadCheckComments(this, <?= $task->id ?>, undefined, <?= $task->step_id ?>)" data-load="0">
                <i class="far fa-plus-square mr-1" data-close="1"></i>
                <i class="far fa-minus-square mr-1" style="display: none" data-open="1"></i>
                Комментарии
            </button>
            <div data-spoiler-content="1" style="display: none">

            </div>
        </div>
    <?php endif; ?>

    <?php if ($task->isLastStep()): ?>
        <?php if (($task->step->last_status ?? 0) == Req3TemplateSteps::LAST_STATUS_SUCCESS): ?>
            <div class="alert alert-default-success mb-0" role="alert" style="flex-basis: 400px; flex-grow: 1;">
                Внимание задача закрыта, успех.
            </div>
        <?php elseif (($task->step->last_status ?? 0) == Req3TemplateSteps::LAST_STATUS_FAILED): ?>
            <div class="alert alert-default-dark mb-0" role="alert" style="flex-basis: 400px; flex-grow: 1;">
                Внимание задача закрыта, провал.
            </div>
        <?php else: ?>
            <div class="alert alert-default-dark mb-0" role="alert" style="flex-basis: 400px; flex-grow: 1;">
                Внимание задача закрыта.
            </div>
        <?php endif; ?>


    <?php endif; ?>

    <?php if ($task->isAutoStep()): ?>
        <div class="alert alert-default-success mb-0" role="alert" style="flex-basis: 400px; flex-grow: 1;">
            Это автоматический шаг.
        </div>
    <?php endif; ?>

    <?php if ($task->isDeviationStep()): ?>
        <div class="alert alert-default-danger mb-0" role="alert" style="flex-basis: 400px; flex-grow: 1;">
            Это шаг отклонения.
        </div>
    <?php endif; ?>

    <?php if ($nextStepSetting['haveTransitions']): ?>
        <?php if (!$task->isAutoStep()): ?>
            <?php if (count($notCompleteIdentifiers) == 0): ?>

                <?php if ($rule2): ?>
                    <?php if ($isExceededTransitions): ?>
                        <div class="alert alert-default-danger mb-0" role="alert" style="flex-basis: 1000px; flex-grow: 1;">
                            <?php if ($task->isAccessResetRuleLimit(Yii::$app->user->identity)): ?>
                                <div class="text-right">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="showDialogTaskResetLimit(this, <?= $task->id ?>, <?= $rule2->id ?>)"><i class="fas fa-redo"></i> Сбросить количество переходов</button>
                                    <?php //TODO сброс количества переходов?>
                                </div>
                            <?php endif; ?>
                            Внимание! Вы использовали все попытки перехода по данному маршруту.
                            <?php if (!empty($rule2->to_step_limit_id)): ?>
                                Поэтому задачу двинется на специальный шаг.
                            <?php else: ?>
                                Обратитесь к руководителю или воспользуйтесь кнопкой "Проблемы в БП"
                            <?php endif; ?>

                        </div>
                    <?php else: ?>
                        <div class="alert alert-default-primary mb-0" role="alert" style="flex-basis: 1000px; flex-grow: 1;">
                            Все обязательные данные заполнены, маршрут определен,
                            <?php if ($task->isLastStep()): ?>
                                можно <b>восстановить</b> задачу.
                            <?php else: ?>
                                можно двигать далее.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-default-danger mb-0" role="alert" style="flex-basis: 1000px; flex-grow: 1;">
                        Все обязательные данные заполнены, но не найден ни один подходящий под условия маршрут.
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php
                $data = [
                    IdentifierCompleteErrors::ERROR_COMPLETE_FILL    => "Для перехода необходимо заполнить:",
                    IdentifierCompleteErrors::ERROR_COMPLETE_REMARKS => "Для перехода необходимо исправить улучшения:",
                ]
                ?>
                <?php foreach ($data as $type => $text): ?>
                    <?php if (isset($notCompleteIdentifiers[$type])): ?>
                        <?php $identifierNames = ArrayHelper::map($notCompleteIdentifiers[$type], 'id', 'name'); ?>
                        <div class="alert alert-default-danger mb-0" role="alert" style="flex-basis: <?= max(1000, count($identifierNames) * 200) ?>px; flex-grow: 1;">
                            <?= $text ?>
                            <div>
                                <?php foreach ($identifierNames as $id => $name): ?>
                                    <a class="badge badge-danger" href="#i_<?= $id ?>" style="white-space: normal; text-align: left; text-decoration: auto;"><?= $name ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!$task->isMyZoneResponsibility(Yii::$app->user->identity)): ?>
                <?php if ($task->isWorkers(Yii::$app->user->identity)): ?>
                    <div class="alert alert-default-warning mb-0" role="alert" style="flex-basis: 400px; flex-grow: 1;">
                        Внимание вы "<b>действующий</b>" на текущем шаге.
                    </div>
                <?php else: ?>
                    <?php $opers = ArrayHelper::getColumn($task->getOpersResponsibility(), 'fio'); ?>
                    <div class="alert alert-default-info mb-0" role="alert" style="flex-basis: <?= max(1000, count($opers) * 200) ?>px; flex-grow: 1;">
                        Внимание текущий шаг <b>НЕ</b> в вашей зоне ответственности. На данном шаге исполняющие:
                        <div>
                            <?php foreach ($opers as $fio): ?>
                                <span class="badge badge-info"><?= $fio ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        <?php endif; ?>
    <?php endif; ?>

</div>
