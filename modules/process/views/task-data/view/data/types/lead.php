<?php

use app\modules\process\models\task_reports\Req3AddressTasksLead;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $link_id int */
/* @var $link_type int */
?>

<a href="<?= Url::toRoute(['/process/report/tasks-lead', 'link_id' => $link_id, 'link_type' => $link_type]) ?>" target="_blank" class="btn btn-light btn-sm">
    Лиды <?= Req3AddressTasksLead::SHORT_NAMES[$link_type] ? "(" . Req3AddressTasksLead::SHORT_NAMES[$link_type] . ")" : "" ?> <span style="color:#939393;">(<?= Req3AddressTasksLead::getCount($link_id, $link_type) ?>)</span>
</a>