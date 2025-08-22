<?php

use app\modules\process\dto\RuleDataDto;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\widgets\IdentifierViewWidget;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $ruleData RuleDataDto */

?>

<?= IdentifierViewWidget::widget([
    'identification' => Yii::$app->user->identity,
    'task'           => $task,
    'ruleData' => $ruleData,
    'identifier'     => $identifier,
]); ?>