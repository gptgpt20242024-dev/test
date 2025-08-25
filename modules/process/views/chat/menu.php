<?php

use app\modules\process\models\chats\Req3TasksChats;

/* @var $this yii\web\View */
/* @var $chat Req3TasksChats */

?>

<div class="dropleft">
    <button class="btn btn-link" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-ellipsis-v"></i>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <?php if ($chat->isAccessClose(Yii::$app->user->identity)): ?>
            <a href="javascript:void (0);" class="dropdown-item text-success" onclick="showDialogChatClose(<?= $chat->id ?>)">
                <i class="fas fa-power-off"></i> Завершить чат
            </a>
        <?php endif; ?>

        <?php if ($chat->isAccessLeave(Yii::$app->user->identity)): ?>
            <a href="javascript:void (0);" class="dropdown-item" onclick="showDialogChatLeave(<?= $chat->id ?>)">
                <i class="fas fa-sign-out-alt"></i> Покинуть чат
            </a>
        <?php endif; ?>

        <?php if ($chat->isAccessAddComplaint(Yii::$app->user->identity)): ?>
            <a href="javascript:void (0);" class="dropdown-item text-danger" onclick="showDialogChatAddComplaint(<?= $chat->id ?>)">
                <i class="fas fa-exclamation-circle"></i> Пожаловаться
            </a>
        <?php endif; ?>

        <?php if ($chat->isAccessInvite(Yii::$app->user->identity)): ?>
            <a href="javascript:void (0);" class="dropdown-item" onclick="showDialogChatInvite(<?= $chat->id ?>)">
                <i class="fas fa-user-plus"></i> Пригласить
            </a>
        <?php endif; ?>

        <a href="javascript:void (0);" class="dropdown-item" onclick="showDialogChatMembers(<?= $chat->id ?>)">
            <i class="fas fa-users"></i> Участники
        </a>


    </div>
</div>
