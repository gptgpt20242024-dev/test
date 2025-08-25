<?php

use app\modules\order\models\TaskProcessLinks;
use app\modules\process\dto\data\TaskDataDto;
use app\modules\process\dto\RuleDataDto;
use app\modules\process\dto\TaskDataLikesDto;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\widgets\TaskDataWidget;
use app\modules\process\widgets\TaskHeaderWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $ruleData RuleDataDto */
/* @var $taskData TaskDataDto */
/* @var $likesData TaskDataLikesDto */
/* @var $notCompleteIdentifiers array */

/* @var $orderLink TaskProcessLinks */
/* @var $orderProjectLinks TaskProcessLinks[] */
?>
<?php if (!($task->version->is_active ?? false)): ?>
    <div class="alert alert-default-warning">
        <b>Внимание!!!</b> Задача работает по старой версии шаблона
    </div>
<?php endif; ?>

<?php if ($task->crash_link): ?>
    <div class="alert alert-default-primary">
        Задача создана автоматически из звонка по <a style="color: #2858ff; font-weight: bold" href="<?= Url::toRoute(['/crash/request/view', 'id' => $task->crash_link->crash_id]) ?>">аварии</a>
    </div>
<?php endif; ?>

<?php if ($orderLink): ?>
    <div class="alert alert-default-primary">
        Задача создана из наряда <a style="color: #2858ff; font-weight: bold" href="<?= Url::toRoute(['/order/task/view', 'id' => $orderLink->task_id]) ?>" target="_blank">#<?= $orderLink->task_id ?></a>
        <?php if (count($orderProjectLinks) > 1): ?>
            (задач по проекту: <a style="color: #2858ff; font-weight: bold" href="<?= Url::toRoute(['/process/task/index', 'task_ids' => ArrayHelper::getColumn($orderProjectLinks, 'process_id'), 'group_by' => ""]) ?>" target="_blank"><?= count($orderProjectLinks) ?></a>)
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($task->started_from_email): ?>
    <div class="alert alert-default-primary">
        Задача создана автоматически из письма (<?= $task->started_from_email->from ?>), фактическая дата письма: <?= $task->started_from_email->date_email_received ?>
    </div>
<?php endif; ?>

<?php if ($task->is_testing): ?>
    <div class="alert alert-default-danger">
        <b>Внимание!!!</b> Это тестовая задача, не воспринимайте её всерьёз!
    </div>
<?php endif; ?>

    <div data-task="<?= $task->id ?>">
        <?= TaskHeaderWidget::widget([
            'task'                   => $task,
            'ruleData'               => $ruleData,
            'notCompleteIdentifiers' => $notCompleteIdentifiers,
        ]) ?>

        <?= TaskDataWidget::widget([
            'task'      => $task,
            'ruleData'  => $ruleData,
            'taskData'  => $taskData,
            'likesData' => $likesData
        ]) ?>

        <?= $this->render('/task-comments/_comments', ['comments' => $task->comments, 'task' => $task]) ?>
    </div>

<?php if ($task->isMyZoneResponsibility(Yii::$app->user->identity)): ?>
    <script type="text/javascript">
        let sessionCode = "<?=bin2hex(random_bytes(10))?>";
        let online = 0;
        let lastSave = -1;

        //пишем сколько секунд он тут провел
        setInterval(function () {
            if (!document.hidden) {
                online++;
            }
        }, 1000);

        $(function () {
            //для подстраховки каждые 10 сек обновляем
            saveTime();
            setInterval(function () {
                saveTime();
            }, 10000);
        });

        //при закрытии вкладки, может не сработать при закрытии браузера
        window.onbeforeunload = function () {
            saveTime();
        }

        document.addEventListener("visibilitychange", function () {
            if (document.hidden) {
                saveTime();
            }
        });

        function saveTime() {
            if (lastSave != online) {
                lastSave = online;
                $.ajax({
                    type: "POST",
                    url: generateUrl('/process/task/ajax-save-online'),
                    data: {
                        _csrf: yii.getCsrfToken(),

                        task_id: <?=$task->id?>,
                        step_id: <?=$task->step_id?>,

                        online: online,
                        session_code: sessionCode,
                    },
                    global: false//не выдавать ошибку
                });
            }
        }

    </script>
<?php endif; ?>