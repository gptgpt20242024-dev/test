<?php

use app\components\Date;
use app\modules\process\components\HelperOper;
use app\modules\process\models\chats\Req3TasksChats;
use app\modules\process\models\task\Req3Tasks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $chats Req3TasksChats[] */
?>

<div data-chats-history="1">
    <?php foreach ($chats as $item): ?>
        <div class="card">
            <div class="card-body">

                <button type="button" class="btn btn-light float-right" onclick="showDialogChat(<?= $item->id ?>)">
                    <i class="fas fa-comments" style="color: #41a2db"></i> Посмотреть
                </button>

                <div>
                    <span style="color: #243a78; font-weight: bold">Тема:</span> <?= $item->topic ?>
                </div>
                <div>
                    <?php $date_create = new Date($item->date_add) ?>
                    <span style="color: #243a78; font-weight: bold">Создал:</span> <?= HelperOper::getFio($item, 'creator_id', 'creator') ?>
                    <span style="color: #737373; font-size: small">(<?= $date_create->format(Date::FORMAT_DATE_TIME) ?>, <?= $date_create->toRemainingText(1) ?> назад.)</span>
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
</div>