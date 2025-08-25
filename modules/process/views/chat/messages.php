<?php

use app\modules\process\models\chats\Req3TasksChatMessages;

/* @var $this yii\web\View */
/* @var $messages Req3TasksChatMessages[] */
?>

<?php foreach ($messages as $message): ?>
    <?php if ($message->type == Req3TasksChatMessages::TYPE_OPER): ?>
        <?= $this->render('message', ['message' => $message]) ?>
    <?php elseif ($message->type == Req3TasksChatMessages::TYPE_INFO): ?>
        <?= $this->render('message_info', ['message' => $message]) ?>
    <?php endif; ?>
<?php endforeach; ?>