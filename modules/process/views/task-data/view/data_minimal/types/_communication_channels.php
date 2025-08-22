<?php

use app\modules\communication\constants\ChannelTypes;
use app\modules\communication\dto\ChannelLinkedDto;
use app\modules\communication\ModuleCommunication;
use app\modules\communication\modules\bitrix\dto\ChannelBitrixDto;
use app\modules\communication\modules\email\dto\ChannelEmailDto;
use app\modules\communication\modules\phone\dto\ChannelPhoneDto;
use app\modules\communication\modules\telegram\dto\ChannelTelegramDto;
use app\modules\communication\modules\viber\dto\ChannelViberDto;
use app\modules\process\models\Req3TasksData;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

// Полнейшие костыли ... надо переделывать на формирование отображаемых данных в контроллере ...

/** @var ModuleCommunication $module */
$module = Yii::$app->getModule('communication');
$canView = $module->canView();

$dto = null;
if ($value->communication_channel_link) {
    $info = null;
    if ($value->communication_channel_link->channel_type == ChannelTypes::PHONE && $value->communication_channel_link->phone) {
        $info = new ChannelPhoneDto($value->communication_channel_link->phone->phone, $value->communication_channel_link->phone->fio);
    }
    if ($value->communication_channel_link->channel_type == ChannelTypes::VIBER && $value->communication_channel_link->viber) {
        $info = new ChannelViberDto($value->communication_channel_link->viber->id_int, $value->communication_channel_link->viber->name);
    }
    if ($value->communication_channel_link->channel_type == ChannelTypes::BITRIX && $value->communication_channel_link->bitrix) {
        $info = new ChannelBitrixDto($value->communication_channel_link->bitrix->bitrix_user_id, $value->communication_channel_link->bitrix->getName());
    }
    if ($value->communication_channel_link->channel_type == ChannelTypes::TELEGRAM && $value->communication_channel_link->telegram) {
        $info = new ChannelTelegramDto($value->communication_channel_link->telegram->id, $value->communication_channel_link->telegram->getName(), $value->communication_channel_link->telegram->phone_number, $value->communication_channel_link->telegram->username);
    }
    if ($value->communication_channel_link->channel_type == ChannelTypes::EMAIL && $value->communication_channel_link->email) {
        $info = new ChannelEmailDto($value->communication_channel_link->email->email, $value->communication_channel_link->email->name);
    }

    $dto = new ChannelLinkedDto(
        null,
        $value->communication_channel_link->channel_id,
        $value->communication_channel_link->channel_type,
        $value->communication_channel_link->type_id,
        $value->communication_channel_link->comment,
        $info
    );

    echo $dto->getValue(!$canView);
} else "-";


?>


