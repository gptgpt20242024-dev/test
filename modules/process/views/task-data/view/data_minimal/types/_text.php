<?php

use app\components\Str;
use app\modules\process\models\task_data\Req3TasksDataItems;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if (Str::isHtmlDocument($value->value_text)): ?>
    <button class="btn btn-light text-primary" type="button" onclick="showDialogHTML(<?= $value->id ?>)">Просмотреть содержимое</button>
<?php else: ?>
    <i><?= Str::toLink(nl2br(Html::encode($value->value_text))) ?></i>
<?php endif; ?>

