<?php

use app\components\Internet;
use app\components\Str;
use app\modules\process\models\task_data\Req3TasksDataItems;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->file): ?>


    <?php if (Str::lower(Str::sub($value->file->orig_name, -3)) == 'pdf'): ?>
        <a href="#" data-file="<?= $value->value_id ?>">
            <i class="fas fa-file-upload"></i>
            <?= $value->file->orig_name ?>
            <span style="color: #7f7f7f; font-style: italic; font-size: small">(<?= Internet::fromBytes($value->file->getFileSize()) ?>)</span>
        </a>
        <script>
            $(function () {
                $("[data-file=<?=$value->value_id?>]").click(function () {
                    let height = $(window).height() - 200;
                    let content = "<iframe style='width: 100%; height: " + height + "px;' src='<?= Url::toRoute(['/process/task/view-file', 'file_id' => $value->value_id])?>'>";
                    let modal = BootstrapDialog.show({
                        size: BootstrapDialog.SIZE_WIDE,
                        type: BootstrapDialog.TYPE_PRIMARY,
                        title: name,
                        message: content
                    });
                });
            });
        </script>
    <?php else: ?>
        <a href="<?= Url::toRoute(['/process/task/get-file', 'file_id' => $value->value_id]) ?>" target="_blank">
            <i class="fas fa-file-upload"></i>
            <?= $value->file->orig_name ?>
            <span style="color: #7f7f7f; font-style: italic; font-size: small">(<?= Internet::fromBytes($value->file->getFileSize()) ?>)</span>
        </a>
    <?php endif; ?>

<?php else: ?>
    file<?= $value->value_id ?>
<?php endif; ?>

