<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $item array */

?>


<?php if ($item['parent']): ?>
    <?= $this->render('node_parents', [
        'task'       => $task,
        'identifier' => $identifier,
        'item'       => $item['parent'],
    ]); ?>
<?php endif; ?>


<?= $this->render('node_parent', [
    'task'       => $task,
    'identifier' => $identifier,
    'node'       => $item['item'],
]); ?>

