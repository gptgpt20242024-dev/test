<?php

use app\components\Date;
use app\modules\process\components\HelperOper;
use app\modules\process\models\chats\Req3TasksChats;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $chat Req3TasksChats */
?>


<div data-footer="1" class="chat-inputs">
    <?php if ($chat->is_active == 0): ?>
        <div style="font-size: small; color: #6e6e6e">
            Чат закрыт
            (<?= HelperOper::getFio($chat, 'close_id', 'close') ?>, <?= $chat->getCloseItemValue() ?>, <?= (new Date($chat->date_close))->format(Date::FORMAT_DATE_TIME) ?>)
        </div>
    <?php else: ?>


        <?php if ($chat->isAccessAddMessage(Yii::$app->user->identity)): ?>
            <?php $form = ActiveForm::begin([
                'options' => [
                    'onsubmit' => "sendMessageToChat({$chat->id}); return false;",
                    'enctype'  => 'multipart/form-data'
                ]
            ]); ?>
            <div class="input-group">
                <div class="input-group-prepend">
                    <label for="file" class="btn btn-outline-light mb-0" style="color: #767676; border: 1px solid #d5d5d5;">
                        <i class="fas fa-paperclip"></i>
                    </label>
                </div>
                <textarea id="chat_message" name="message" placeholder="Введите сообщение" class="form-control" rows="1"></textarea>
                <script>
                    let $messageTextArea = $('[id="chat_message"]');
                    autosize($messageTextArea);
                    $messageTextArea.keydown(function (event) {
                        if (event.shiftKey && event.keyCode === 13) {
                            return true;
                        }

                        if (!event.shiftKey && event.keyCode === 13) {
                            sendMessageToChat(<?= $chat->id ?>);
                            return false;
                        }
                    })
                </script>

                <div class="input-group-append">
                    <button type="button" class="btn btn-info" onclick="sendMessageToChat(<?= $chat->id ?>)" style="display: flex;">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
            <div data-file-name="1" style="font-weight: bold; font-size: small; color: #3f6a8f; font-style: italic;"></div>
            <input type="file" name="file[]" id="file" style="display: none" onchange="showSelectFileName(this)" multiple>
            <?php ActiveForm::end() ?>
        <?php else: ?>
            <?php if ($chat->isAccessConnectSelf(Yii::$app->user->identity)): ?>
                <button type="button" class="btn btn-info" onclick="connectToChat(<?= $chat->id ?>)">
                    Присоединиться
                </button>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>