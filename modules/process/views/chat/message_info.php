<?php

use app\components\Str;
use app\modules\process\models\chats\Req3TasksChatMessages;

/* @var $this yii\web\View */
/* @var $message Req3TasksChatMessages */
?>

<div class="direct-chat-msg" id="msg<?= $message->id ?>" style="text-align: center">
    <div style="color: #9d9d9d;font-size: small;background-color: #e7e7e7;padding: 3px 10px;border-radius: 10px; display: inline-block">
        <?= Str::toLink($message->message) ?>
    </div>
</div>