<?php

use app\components\Date;
use app\modules\process\components\HelperOper;
use app\modules\process\models\chats\Req3TasksChats;

/* @var $this yii\web\View */
/* @var $chat Req3TasksChats */
?>

<div data-chat-members="1">
    <div class="list-group">
        <?php foreach ($chat->members as $member): ?>
            <div class="list-group-item" style="<?= (!empty($member->date_left)) ? "background-color: #ffe2e2" : "" ?>">

                <?php if ($member->oper_id == $chat->creator_id): ?>
                    <i class="fas fa-star float-right" style="color: #6eaadf" title="Создатель чата"></i>
                <?php endif; ?>

                <?php if ($member->is_controller == 1): ?>
                    <i class="far fa-star float-right" style="color: #bdbdbd" title="Ответственный по задаче"></i>
                <?php endif; ?>

                <?php if ($member->is_executor == 1): ?>
                    <i class="far fa-star-half float-right" style="color: #bdbdbd" title="Исполнитель по задаче"></i>
                <?php endif; ?>

                <b><?= HelperOper::getFio($member, 'oper_id', 'oper') ?></b>
                <?php if (!empty($member->invited_id)): ?>
                    <div style="color: #707070; font-size: small;">
                        Добавил(-а): <?= HelperOper::getFio($member, 'invited_id', 'invited') ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($member->date_left)): ?>
                    <div style="color: #b21717; font-size: small;">
                        <?php $date = new Date($member->date_left); ?>
                        Покинул(-а) чат: <?= $date->format(Date::FORMAT_DATE_TIME) ?> (<?= $member->left_item->value ?? "-" ?>)
                    </div>
                <?php endif; ?>

                <?php if (!empty($member->date_last_see)): ?>
                    <div style="color: #707070; font-size: small;">
                        <?php $date = new Date($member->date_last_see); ?>
                        Последний раз смотрел(-а) в чат: <?= $date->format("d.m.Y H:i:s") ?> (<?= $date->toRemainingText(1) ?> назад)
                    </div>
                <?php else: ?>
                    <div style="color: #9d5959; font-size: small;">
                        Еще ни разу не заходил(-а) в чат
                    </div>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>
</div>