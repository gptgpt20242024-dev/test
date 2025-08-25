<?php

use app\components\Str;
use app\models\Opers;
use app\modules\process\models\chats\Req3TasksChats;

/* @var $this yii\web\View */
/* @var $chat Req3TasksChats */

$key = Str::generateRandom();
?>

<div style="display: flex; align-items: center; flex-direction: column;">
    <div style="height: calc(95vh - 57px - 15px - 30px - 1rem); box-shadow: 2px 2px 20px rgba(0, 0, 0, 0.4); max-width: 850px; width: 100%">
        <div class="card card-info card-outline" style="display: flex; flex-direction: column; height: 100%;" id="chat<?= $chat->id ?>" data-session-key="<?= $key ?>" data-last-message-id="<?= $chat->last_message_id ?>" data-is-active="<?= $chat->is_active ?>">

            <div class="card-header" style="background-color: #ebebeb;">
                <div style="display: flex; align-items: flex-start; flex-direction: row; gap: 10px;">
                    <div style="flex: 1">
                        <?= $chat->topic ?>
                        <div style="font-size: small; color: #858585"><?= Opers::getFioOrFioDeletedHtml($chat, 'creator', 'creator_id') ?></div>
                    </div>

                    <?= $this->render('menu', ['chat' => $chat]) ?>
                </div>
            </div>

            <div class="direct-chat direct-chat-lightblue" style="flex: 1; overflow-y: auto">
                <div class="direct-chat-messages" style="height: auto;" data-messages="1">
                    <?= $this->render('messages', ['messages' => $chat->messages]) ?>
                </div>

                <script>
                    $(function () {
                        scrollChatToBottom(<?=$chat->id?>);
                        checkNewMessages(<?=$chat->id?>, "<?=$key?>");
                    });
                </script>
            </div>

            <div class="card-footer">
                <?= $this->render('footer', ['chat' => $chat]) ?>
            </div>

            <script>
                $(function () {
                    initViewer("#chat<?= $chat->id ?>", '.js-img');
                });
            </script>
        </div>
    </div>
</div>