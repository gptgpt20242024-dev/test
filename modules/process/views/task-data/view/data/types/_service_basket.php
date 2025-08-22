<?php

use app\components\Str;
use app\modules\process\components\HelperOper;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItemBaskets;
use app\modules\process\models\task_data\Req3TasksDataItems;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

?>


<?php if (!$value->basket): ?>
    <div class="px-2 py-2" data-empty-basket="1">
        <div class="alert alert-default-warning mb-0">
            Корзина не создана
        </div>

        <?php if ($is_editable && !$is_only_view && $can_edit): ?>
            <div class="mt-2" style="display: flex; flex-direction: column;align-items: center; gap: 5px;">
                <button type="button" class="btn btn-outline-success" style="width: 200px; max-width: 100%" onclick="showDialogBasketService(<?= $task->id ?>, <?= $identifier->id ?>)">Добавить</button>
                <button type="button" class="btn btn-link" data-spoiler data-container="[data-empty-basket]">Привязаться</button>


                <div class="mt-2" style="display: none; " data-spoiler-content="1">
                    <div class="form-group field-installment_value" style="text-align: center">
                        <?php $name = "link_basket_id"; ?>
                        <?php $id = Html::getInputIdByName($name); ?>
                        <label class="control-label mb-0">Введите номер корзины</label>

                        <div class="input-group">

                            <?= Html::input('number',
                                $name,
                                null,
                                ['id' => $id, 'class' => 'form-control']
                            ) ?>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-primary" onclick="linkBasket(this, <?= $task->id ?>, <?= $identifier->id ?>)">
                                    <i class="fas fa-link"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>


        <?php endif; ?>
    </div>

<?php else: ?>
    <?php if (count($value->basket->services) == 0): ?>
        <div class="px-2 py-2">
            <div class="alert alert-default-warning">
                Корзина пустая
            </div>

            <?php if ($is_editable && !$is_only_view && $can_edit): ?>
                <button type="button" class="btn btn-outline-success" style="width: 200px; max-width: 100%" onclick="showDialogBasketService(<?= $task->id ?>, <?= $identifier->id ?>)">Добавить</button>
            <?php endif; ?>
        </div>
    <?php else: ?>

        <ul class="list-group list-group-flush">
            <?php foreach ($value->basket->services as $service): ?>
                <li class="list-group-item">
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-start;">

                        <div style="flex: 1; flex-basis: 200px">
                            <div style="display: flex; flex-wrap: wrap;">
                                <div style="flex: 1; flex-basis: 200px">
                                    <?= $service->reward_service->name ?? "<span style='color: #80383a; text-decoration: line-through'>delete id{$service->reward_service_id}</span>" ?>
                                </div>
                                <div style="color: #d2d2d2; font-size: small">
                                    (<?= Str::number_format2($service->price, 0) ?>р.)
                                </div>
                            </div>
                            <div class="badge badge-secondary" data-spoiler data-container=".list-group-item">
                                Описание услуги
                            </div>
                            <p class="text-gray mt-3" data-spoiler-content style="display: none">
                                <?= $service->reward_service->description ?? 'Описание услуги отсутствует' ?>
                            </p>
                            <div style="color: #858585; font-size: small">
                                <?= HelperOper::getFio($service, 'oper_id', 'oper') ?>
                            </div>

                            <div>
                                <?php if ($service->order_task_id): ?>
                                    <a class="badge badge-primary" href="<?= Url::toRoute(['/order/task/view', 'id' => $service->order_task_id]) ?>">Наряд</a>
                                <?php endif; ?>
                                <?php if ($service->service_link_id): ?>
									<a class="badge badge-warning" href="<?= Url::toRoute(['/user1/profile/view', 'user_id' => $service->service_link->account->user->user_id ?? -1]) ?>">ЛС</a>
                                <?php elseif ($service->services2_link_id): ?>
									<a class="badge badge-warning" href="<?= Url::toRoute(['/user1/profile/view', 'user_id' => $service->services2_link->user_id ?? -1]) ?>">ЛС</a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($is_editable && !$is_only_view && $can_edit): ?>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteBasketService(this, <?= $task->id ?>, <?= $identifier->id ?>, <?= $service->id ?>)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <?php endif; ?>

                </li>
            <?php endforeach; ?>

            <?php if ($value->basket->installment_type): ?>
                <li class="list-group-item">
                    <b style="font-weight: bold;">
                        Рассрочка на: <?= $value->basket->installment_value ?>
                        <?= $value->basket->installment_type == Req3TasksDataItemBaskets::INSTALLMENT_TYPE_MONTH_COUNT ? "месяц." : "" ?>
                        <?= $value->basket->installment_type == Req3TasksDataItemBaskets::INSTALLMENT_TYPE_MONTHLY_AMOUNT ? "р. в месяц." : "" ?>
                    </b>
                </li>
            <?php endif; ?>

            <?php if ($is_editable && !$is_only_view && $can_edit): ?>
                <li class="list-group-item" style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <button type="button" class="btn btn-outline-success" onclick="showDialogBasketService(<?= $task->id ?>, <?= $identifier->id ?>)">Добавить</button>
                    <button type="button" class="btn btn-outline-secondary" data-spoiler data-container=".list-group">Рассрочка</button>
                </li>

                <li class="list-group-item" style="display: none" data-spoiler-content="1">
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <div class="form-group">
                            <?php $name = "installment_type"; ?>
                            <?php $id = Html::getInputIdByName($name); ?>
                            <label class="control-label mb-0">Тип рассрочки</label>
                            <?= Html::radioList(
                                $name,
                                $value->basket->installment_type,
                                [null => "Без рассрочки"] + Req3TasksDataItemBaskets::INSTALLMENT_TYPE_NAMES,
                                [
                                    'id'       => $id,
                                    'onchange' => "initFieldsWithRadioList('{$id}', [[['1', '2'], ['.field-installment_value']]]);",
                                ]
                            ) ?>
                        </div>

                        <div class="form-group field-installment_value" style="<?= $value->basket->installment_type == null ? "display:none;" : "" ?>">
                            <?php $name = "installment_value"; ?>
                            <?php $id = Html::getInputIdByName($name); ?>
                            <label class="control-label mb-0">Значение рассрочки</label>
                            <?= Html::input('number',
                                $name,
                                $value->basket->installment_value,
                                ['id' => $id, 'class' => 'form-control']
                            ) ?>
                        </div>

                    </div>
                    <button type="button" class="btn btn-outline-primary" onclick="saveBasketInstallment(this, <?= $task->id ?>, <?= $identifier->id ?>, <?= $value->basket->id ?>)">Установить рассрочку</button>
                </li>
            <?php endif; ?>


        </ul>
    <?php endif; ?>
<?php endif; ?>