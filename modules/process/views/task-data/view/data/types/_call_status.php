<?php

use app\assets\AudioPlayer;
use app\components\Date;
use app\components\Phone;
use app\models\Opers;
use app\modules\process\components\HelperGetCalls;
use app\modules\process\models\calls\Req3CallsStatuses;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */


AudioPlayer::register($this);
?>


<div class="list-group list-group-flush">
    <?php foreach ($task->calls_statuses as $status): ?>


        <?php if ($status->reason == Req3CallsStatuses::REASON_NO_NUMBERS): ?>
            <div class="list-group-item px-0">
                <div style="font-size: small; color: #676767; display: flex; margin-bottom: 5px; border-bottom: 1px solid #eeeeee">
                    <?php $date = (new Date($status->date_update)); ?>
                    <div>
                        <?= $date->format(Date::FORMAT_DATE_TIME); ?>
                        <span style="color: #909090">(<?= $date->toRemainingText(1, true) ?>)</span>
                    </div>
                </div>
                <i><?= $status->getStatusName() ?></i> - <?= $status->getReasonName() ?>
            </div>
        <?php endif; ?>


        <?php foreach ($status->status_phones as $status_phone): ?>
            <div class="list-group-item px-0">
                <div style="font-size: small; color: #676767; display: flex; margin-bottom: 5px; border-bottom: 1px solid #eeeeee">
                    <?php $date = (new Date($status_phone->date_set)); ?>
                    <div>
                        <?= $date->format(Date::FORMAT_DATE_TIME); ?>
                        <span style="color: #909090">(<?= $date->toRemainingText(1, true) ?>)</span>
                    </div>

                    <div style="margin-left: auto">
                        <?php if ($status_phone->oper): ?>
                            <?= $status_phone->oper->fio ?>
                        <?php else: ?>
                            <span style="color: #6f433c; text-decoration:line-through;"><?= Opers::getFio($status_phone->oper_id) ?></span>
                        <?php endif; ?>
                    </div>

                </div>

                <b><?= Phone::format($status_phone->phone) ?></b>:
                <?php if ($status_phone->count_calls > 1): ?>
                    <span style="color: #296789">(Попытка <?= $status_phone->count_calls ?>/<?= HelperGetCalls::COUNT_CALL ?>)</span>
                <?php endif; ?>

                <i><?= $status_phone->getStatusName() ?></i>


                <?php if (!empty($status_phone->url) > 0): ?>
                    <div>
                        <div style="display: flex; align-items: center">

                            <button class="btn btn-outline-primary" data-play="<?= $status_phone->id ?>">
                                <i class="fas fa-play"></i>
                                <i class="fas fa-pause" style="display: none"></i>
                            </button>

                            <div id="waveform<?= $status_phone->id ?>" style="width: 100%; overflow: hidden">

                            </div>
                        </div>


                        <script>
                            $(function () {
                                let $btn = $('[data-play="<?= $status_phone->id ?>"]');

                                let player = WaveSurfer.create({
                                    container: '#waveform<?=$status_phone->id?>',
                                    waveColor: '#98c6ff',
                                    progressColor: '#007bff',
                                    forceDecode: true,
                                    backend: 'MediaElement',
                                    hideScrollbar: true,
                                    height: 50

                                });
                                player.load('<?=Url::toRoute(['/process/oktell/get-audio', 'status_phone_id' => $status_phone->id]) ?>');

                                player.on('pause', function () {
                                    $btn.find(".fa-play").show();
                                    $btn.find(".fa-pause").hide();
                                });
                                player.on('play', function () {
                                    $btn.find(".fa-play").hide();
                                    $btn.find(".fa-pause").show();
                                });

                                $('[data-play="<?= $status_phone->id ?>"]').click(function () {
                                    player.playPause();
                                })
                            });
                        </script>
                    </div>
                <?php endif; ?>


            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>
