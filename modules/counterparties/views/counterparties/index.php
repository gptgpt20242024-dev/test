<?php

use app\modules\counterparties\models\Counterparties;
use app\modules\counterparties\models\CounterpartiesPhysFace;
use app\modules\counterparties\models\FormFindCounterparties;
use kartik\widgets\Select2;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model FormFindCounterparties */
/* @var $can_add boolean */
/* @var $can_delete boolean */
/* @var $counterparties Counterparties[] */
?>

<div class="row justify-content-center">
    <div class="col-lg-10">

        <?php if ($can_add): ?>
            <a type="button" class="btn btn-success mb-3"
               href="<?= Url::toRoute(['add', 'type' => Counterparties::LINK_TYPE_PHYS_FACE]) ?>">
                Добавить
            </a>
        <?php endif; ?>


        <?php $form = ActiveForm::begin([
            'method' => 'GET',
            'action' => Url::toRoute(["/counterparties/counterparties/index"])
        ]); ?>
        <div class="card">
            <div class="card-header">
                <h2 class="m-0">Фильтр</h2>
            </div>
            <div class="card-body">

                <?php $init_value = $model->user->login ?? ""; ?>
                <?= $form->field($model, 'user_id')->widget(Select2::class, [
                    'initValueText' => $init_value,
                    'pluginOptions' => [
                        'placeholder' => 'сделайте выбор',
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'ajax' => [
                            'url' => Url::toRoute(["/user/json-select2-find"]),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {text:params.term}; }'),
                            'delay' => 1000
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(user) { return user.find; }'),
                    ],
                ])->hint($this->render('@app/views/user/ajax_hint_find')); ?>

                <?= $form->field($model, 'types')->widget(Select2::class, [
                    'data' => Counterparties::LINK_TYPE_NAMES,
                    'options' => ['multiple' => true,],
                    'pluginOptions' => ['allowClear' => true, 'placeholder' => 'сделайте выбор'],
                ]); ?>

                <?= $form->field($model, 'name') ?>

                <?= $form->field($model, 'phone') ?>

                <?= $form->field($model, 'email') ?>

            </div>
            <div class="card-footer">
                <?= Html::submitButton("Искать", ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end() ?>


        <div class="row">
            <?php foreach ($counterparties as $counterparty): ?>
                <div class="col-3xl-6">
                    <div class="card">
                        <div class="card-header">

                            <div class="ribbon-wrapper">
                                <?php if ($counterparty->link_type == Counterparties::LINK_TYPE_PHYS_FACE): ?>
                                    <div class="ribbon bg-<?= $counterparty->state == Counterparties::STATE_DRAFT ? "secondary" : "primary" ?>">
                                        Физ
                                    </div>
                                <?php endif; ?>

                                <?php if ($counterparty->link_type == Counterparties::LINK_TYPE_ORGANIZATION): ?>
                                    <div class="ribbon bg-<?= $counterparty->state == Counterparties::STATE_DRAFT ? "secondary" : "success" ?>">
                                        Орг
                                    </div>
                                <?php endif; ?>

                                <?php if ($counterparty->link_type == Counterparties::LINK_TYPE_IP): ?>
                                    <div class="ribbon bg-<?= $counterparty->state == Counterparties::STATE_DRAFT ? "secondary" : "warning" ?>">
                                        ИП
                                    </div>
                                <?php endif; ?>

                            </div>

                            <?php if ($counterparty->link_type == Counterparties::LINK_TYPE_PHYS_FACE && $counterparty->phys_face): ?>
                                <h3 class="m-0"><a
                                            href="<?= Url::toRoute(['view', 'id' => $counterparty->id]) ?>"><?= $counterparty->phys_face->getTitle() ?></a>
                                </h3>
                                <?php if ($counterparty->phys_face->doc_type == CounterpartiesPhysFace::DOC_TYPE_PASSPORT_RF): ?>
                                    <div style="color: #3b3b3b">Документ: Паспорт РФ</div>
                                <?php endif; ?>
                                <?php if ($counterparty->phys_face->doc_type == CounterpartiesPhysFace::DOC_TYPE_RWP): ?>
                                    <div style="color: #3b3b3b">Документ: РВП</div>
                                <?php endif; ?>
                                <?php if ($counterparty->phys_face->doc_type == CounterpartiesPhysFace::DOC_TYPE_RESIDENCE): ?>
                                    <div style="color: #3b3b3b">Документ: Вид на жительство</div>
                                <?php endif; ?>
                                <?php if ($counterparty->phys_face->doc_type == CounterpartiesPhysFace::DOC_TYPE_NONE): ?>
                                    <div style="color: #96362b"><i class="far fa-times-circle"></i> Без документа</div>
                                <?php endif; ?>

                            <?php endif; ?>

                            <?php if ($counterparty->link_type == Counterparties::LINK_TYPE_ORGANIZATION && $counterparty->organization): ?>
                                <h3 class="m-0"><a
                                            href="<?= Url::toRoute(['view', 'id' => $counterparty->id]) ?>"><?= $counterparty->organization->getTitle() ?></a>
                                </h3>
                                <div style="color: #3b3b3b">ИНН: <?= $counterparty->organization->inn ?></div>
                            <?php endif; ?>

                            <?php if ($counterparty->link_type == Counterparties::LINK_TYPE_IP && $counterparty->ip): ?>
                                <h3 class="m-0"><a
                                            href="<?= Url::toRoute(['view', 'id' => $counterparty->id]) ?>"><?= $counterparty->ip->getTitle() ?></a>
                                </h3>
                                <div style="color: #3b3b3b">ИНН: <?= $counterparty->ip->inn ?></div>
                            <?php endif; ?>
                        </div>


                        <div class="card-footer">
                            <?php if ($can_delete): ?>
                                <button type="button" class="btn btn-sm btn-danger js_delete_counterparty"
                                        data-id="<?= $counterparty->id ?>">Удалить
                                </button>
                            <?php endif; ?>
                            <?= $this->render('view-info-add', ['counterparty_global' => $counterparty]) ?>
                        </div>


                        <?php if ($can_delete): ?>
                            <div class="overlay dark" style="display: none">
                                <i class="fas fa-3x fa-trash-alt shadow" style="color: #ff706a"></i>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($can_delete): ?>
                <script>
                    $(function () {
                        $(".js_delete_counterparty").click(function () {
                            let $this = $(this);
                            let $overlay = $(this).closest(".card").find(".overlay");
                            let counterparty_id = $this.data('id');

                            BootstrapDialog.show({
                                size: BootstrapDialog.SIZE_NORMAL,
                                type: BootstrapDialog.TYPE_DANGER,
                                title: "Подтверждение",
                                message: "Вы уверены что хотите удалить контрагента ?",
                                buttons: [
                                    BootstrapDialog.cancelButton(),
                                    {
                                        label: 'Подтвердить',
                                        cssClass: "btn-danger",
                                        hotkey: 13, // Enter.
                                        action: function (dialog) {
                                            $this.addClass('disabled');
                                            $this.prop('disabled', true);

                                            deleteCounterparty(counterparty_id, function () {
                                                $overlay.show();
                                            }, function () {
                                                $this.removeClass('disabled');
                                                $this.removeAttr('disabled');
                                            });
                                            dialog.close();
                                        }
                                    }
                                ]
                            });
                            return false;
                        });
                    });

                    function deleteCounterparty(counterparty_id, onComplete, onError) {
                        $.ajax({
                            url: "<?=Url::toRoute(['/counterparties/counterparties/ajax-delete'])?>",
                            dataType: "json",
                            method: "POST",
                            data: {
                                counterparty_id: counterparty_id
                            },
                            success: function (data) {
                                if (data.result) {
                                    PNotify.success({title: 'Успех', text: 'Контрагент успешно удален.'});
                                    if (typeof onComplete === "function") onComplete();
                                } else {
                                    if (typeof onError === "function") onError();
                                }
                            },
                            error: function (data) {
                                if (typeof onError === "function") onError();
                            }
                        });
                    }
                </script>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php if (isset($pager) && $pager != null && $pager->pageCount > 1): ?>
    <?= LinkPager::widget([
        'listOptions' => ['class' => 'pagination justify-content-center'],
        'pagination' => $pager,
        'firstPageLabel' => 'Первая',
        'lastPageLabel' => 'Последняя (' . $pager->pageCount . ')',
        'prevPageLabel' => '&laquo;',
        'nextPageLabel' => '&raquo;',
        'maxButtonCount' => 5,
    ]);
    ?>
<?php endif; ?>
