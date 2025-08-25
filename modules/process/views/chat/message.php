<?php

use app\components\Date;
use app\components\FileHelper;
use app\components\Internet;
use app\components\Str;
use app\models\Opers;
use app\modules\process\models\chats\Req3TasksChatMessages;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $message Req3TasksChatMessages */
?>

<?php
$class_name = "float-left";
$class_date = "float-right";
$class_msg = "";
if ($message->oper_id == Yii::$app->user->id) {
    $class_name = "float-right";
    $class_date = "float-left";
    $class_msg = "right";
}
$date = new Date($message->date_add);
$oper_name = Opers::getFioOrFioDeletedHtml($message);

$md5_hash = md5($oper_name);
$color = '#' . substr($md5_hash, 0, 6) . "55";

?>

<div class="direct-chat-msg <?= $class_msg ?>" id="msg<?= $message->id ?>">
    <div class="direct-chat-infos clearfix">
        <span class="direct-chat-name <?= $class_name ?>"><?= $oper_name ?></span>
        <span class="direct-chat-timestamp <?= $class_date ?>"><?= $date->format(Date::FORMAT_DATE_TIME) ?></span>
    </div>

    <span class="direct-chat-img" style="background-color: <?= $color ?>;text-align: center;line-height: 40px;font-weight: bold;font-size: 20px;">
        <?= Str::sub($oper_name, 0, 1) ?>
    </span>

    <div class="direct-chat-text">
        <?= nl2br(Str::toLink(Html::encode($message->message))) ?>
    </div>

    <?php if (count($message->files) > 0): ?>
        <div class="mt-1">
            <?php foreach ($message->files as $file): ?>
                <div>
                    <?php if (FileHelper::isImg($file->orig_name)): ?>
                        <a href="<?= Url::toRoute(['/process/chat/get-file', 'file_id' => $file->id]) ?>" target="_blank" class="js-img">
                            <i class="fas fa-file-upload"></i>
                            <?= $file->orig_name ?>
                            <span style="color: #7f7f7f; font-style: italic; font-size: small">(<?= Internet::fromBytes($file->getFileSize()) ?>)</span>
                        </a>
                    <?php else: ?>
                        <a href="<?= Url::toRoute(['/process/chat/get-file', 'file_id' => $file->id]) ?>" target="_blank">
                            <i class="fas fa-file-upload"></i>
                            <?= $file->orig_name ?>
                            <span style="color: #7f7f7f; font-style: italic; font-size: small">(<?= Internet::fromBytes($file->getFileSize()) ?>)</span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>