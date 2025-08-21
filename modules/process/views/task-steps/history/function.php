<?php

use app\components\Date;
use app\modules\process\components\HelperOper;
use app\modules\process\models\Req3FunctionBase;

/* @var $this yii\web\View */
/* @var $item array */
?>


<div style="margin-left: 60px; font-size: small; color: #815987;" title="Информация о запущенной Функции">


    <?php if (isset($item['name'])): ?>
        <div>
            <i class="fas fa-meteor"></i> <?= $item['name'] ?>
        </div>
    <?php endif; ?>

    <?php if ($item['type'] == Req3FunctionBase::TYPE_CLICK_BTN): ?>
        <?php if (isset($item['time_start'])): ?>
            <div>
                <i class="fas fa-clock"></i> <?= (new Date($item['time_start']))->format(Date::FORMAT_DATE_TIME) ?>
                <?php if (isset($item['oper_id'])): ?>
                    <span style="color: #9985af">(<?= HelperOper::getFioById($item['oper_id']) ?>)</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($item['btn_name'])): ?>
            <div>
                <i class="fas fa-mouse"></i> <?= $item['btn_name'] ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <?php if (isset($item['data']) && count($item['data']) > 0): ?>
        <div>
            <div><i class="fas fa-database"></i> Данные:</div>
            <?php foreach ($item['data'] as $error): ?>
                <div style="margin-left: 20px"><?= $error ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($item['errors']) && count($item['errors']) > 0): ?>
        <div style="color: #c54a4a">
            <div><i class="fas fa-exclamation-circle"></i> Ошибки:</div>
            <?php foreach ($item['errors'] as $error): ?>
                <div style="margin-left: 20px"><?= $error ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>


</div>
