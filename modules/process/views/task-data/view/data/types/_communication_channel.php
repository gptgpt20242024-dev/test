<?php

use app\modules\communication\dto\ChannelLinkedDto;
use app\modules\communication\dto\GroupChannelLinkedDto;
use app\modules\communication\services\CommunicationService;
use app\modules\communication\widgets\WidgetViewGroupChannels;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */
/* @var $values Req3TasksDataItems[] */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

$channels = [];
foreach ($values as $value) {
    if ($value->communication_channel_link) {
        $channels[] = new ChannelLinkedDto(
            -1,
            $value->communication_channel_link->channel_id,
            $value->communication_channel_link->channel_type,
            $value->communication_channel_link->type_id,
            $value->communication_channel_link->comment
        );;
    }
}

$communicationService = Yii::$container->get(CommunicationService::class);
$communicationService->loadInfo($channels);

echo WidgetViewGroupChannels::widget(['channels' => new GroupChannelLinkedDto($channels), 'canAction' => false, 'params' => ['fmId' => $task->fm_id]]);



