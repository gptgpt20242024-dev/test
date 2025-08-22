<?php

use app\modules\address\models\Locations;
use app\modules\counterparties\models\Counterparties;
use app\modules\counterparties\models\CounterpartiesIp;
use app\modules\counterparties\models\CounterpartiesOrganization;
use app\modules\counterparties\models\CounterpartiesPhysFace;
use app\modules\counterparties\widgets\WidgetCounterpartyCommunications;
use app\modules\process\dto\IdentifierRuleDto;
use app\modules\process\dto\RuleDataDto;
use app\modules\process\models\identifiers\Req3IdentifierDetails;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\user\models\Users;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $ruleData ?RuleDataDto */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

if (count($identifier->details) == 0) {
    $identifier->populateRelation('details', [
        new Req3IdentifierDetails(['type' => Req3IdentifierDetails::TYPE_COUNTERPARTY_NAME])
    ]);
}

/** @var IdentifierRuleDto[] $identifiersUsersEditable */
$identifiersUsersEditable = array_filter($ruleData->identifiers ?? [], fn(IdentifierRuleDto $it) => $it->isEditable() && $it->identifier->type == Req3Identifiers::TYPE_USER);

$status = null;
if (($value->counterparty->link_type ?? null) == Counterparties::LINK_TYPE_IP && ($value->counterparty->ip->status ?? CounterpartiesOrganization::STATUS_ACTIVE) != CounterpartiesIp::STATUS_ACTIVE) {
    $status = $value->counterparty->ip->getStatusName();
}
if (($value->counterparty->link_type ?? null) == Counterparties::LINK_TYPE_ORGANIZATION && ($value->counterparty->organization->status ?? CounterpartiesOrganization::STATUS_ACTIVE) != CounterpartiesOrganization::STATUS_ACTIVE) {
    $status = $value->counterparty->organization->getStatusName();
}
?>


<?php if ($value->counterparty): ?>
    <?php foreach ($identifier->details as $detail): ?>
        <div>
            <?php if ($detail->type != Req3IdentifierDetails::TYPE_COUNTERPARTY_NAME): ?>
                <span style="font-weight: bolder"><?= $detail->getCounterpartyTypeName() ?>:</span>
            <?php endif; ?>

            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_COUNTERPARTY_NAME): ?>
                <a href="<?= Url::toRoute(['/counterparties/counterparties/view', 'id' => $value->counterparty->id]) ?>" target="_blank">
                    <?= $value->counterparty->getTitle() ?>
                </a>
            <?php endif; ?>
            <?php //==============================================================?>

            <?php if ($detail->type == Req3IdentifierDetails::TYPE_COUNTERPARTY_USER_ACCOUNTS): ?>
                <?php if (count($value->counterparty->link_users) > 0): ?>
                    <?php $users = [];
                    foreach ($value->counterparty->link_users as $link_user) {
                        if ($link_user->user) $users[] = $link_user->user;
                    }

                    usort($users, function ($a, $b) {
                        /* @var $a Users */
                        /* @var $b Users */
                        $address1 = $a->getAddressFullName();
                        $address2 = $b->getAddressFullName();
                        return strnatcmp($address1, $address2);
                    });
                    ?>

                    <?php foreach ($users as $i => $user): ?>
                        <div style="padding-left :15px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden">
                            <a href="<?= Url::toRoute(["/user1/profile/view", 'user_id' => $user->user_id]) ?>" target="_blank"><?= $user->utm_acc_id ?></a>
                            <span style="font-size: small;">
                                <?= $user->getAddressFullName(Locations::TYPE_REGION, true, true) ?>
                            </span>

                            <?php foreach ($identifiersUsersEditable as $identUser): ?>
                                <button type="button" class="btn btn-xs btn-light" onclick="saveUserIdentifier(<?= $task->id ?>, <?= $identUser->identifier->id ?>, <?= $user->user_id ?>)">в <?= $identUser->identifier->name ?></button>
                            <?php endforeach; ?>

                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    нет.
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_COUNTERPARTY_INN): ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_IP && $value->counterparty->ip): ?>
                    <?= $value->counterparty->ip->inn ?>
                <?php endif; ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_ORGANIZATION && $value->counterparty->organization): ?>
                    <?= $value->counterparty->organization->inn ?>
                <?php endif; ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_PHYS_FACE): ?>
                    ---
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_COUNTERPARTY_JUR_ADDRESS): ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_IP && $value->counterparty->ip): ?>
                    <?= $value->counterparty->ip->address ?>
                <?php endif; ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_ORGANIZATION && $value->counterparty->organization): ?>
                    <?= $value->counterparty->organization->address ?>
                <?php endif; ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_PHYS_FACE): ?>
                    ---
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_COUNTERPARTY_MAIL_ADDRESS): ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_IP && $value->counterparty->ip): ?>
                    <?= $value->counterparty->ip->address_mailing ?>
                <?php endif; ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_ORGANIZATION && $value->counterparty->organization): ?>
                    <?= $value->counterparty->organization->address_mailing ?>
                <?php endif; ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_PHYS_FACE): ?>
                    ---
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_COUNTERPARTY_COMMUNICATIONS): ?>
                <?= WidgetCounterpartyCommunications::widget([
                    'counterpartyId' => $value->value_id,
                    'params'         => ['fmId' => $task->fm_id],
                ]) ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_COUNTERPARTY_REPRESENTATIVE_NAME): ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_IP && $value->counterparty->ip): ?>
                    <?= $value->counterparty->ip->representative_name ?>
                <?php endif; ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_ORGANIZATION && $value->counterparty->organization): ?>
                    <?= $value->counterparty->organization->representative_name ?>
                <?php endif; ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_PHYS_FACE): ?>
                    ---
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_COUNTERPARTY_REPRESENTATIVE_POSITION): ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_IP && $value->counterparty->ip): ?>
                    <?= $value->counterparty->ip->representative_position ?>
                <?php endif; ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_ORGANIZATION && $value->counterparty->organization): ?>
                    <?= $value->counterparty->organization->representative_position ?>
                <?php endif; ?>
                <?php if ($value->counterparty->link_type == Counterparties::LINK_TYPE_PHYS_FACE): ?>
                    ---
                <?php endif; ?>
            <?php endif; ?>
            <?php //==============================================================?>
            <?php //==============================================================?>
            <?php if ($detail->type == Req3IdentifierDetails::TYPE_COUNTERPARTY_DOC_TYPE): ?>
                <?= $value->counterparty->getDocTypeName() ?>
            <?php endif; ?>
            <?php //==============================================================?>

        </div>
    <?php endforeach; ?>


    <?php
    //дикий костыль без проверки на сервере
    $editable_access = false;
    if ($value->counterparty) {
        if ($value->counterparty->link_type == Counterparties::LINK_TYPE_PHYS_FACE && $value->counterparty->phys_face) {
            if ($value->counterparty->phys_face->doc_type == CounterpartiesPhysFace::DOC_TYPE_NONE) {
                $editable_access = true;
            }
        }
    }
    ?>

    <?php if ($editable_access && $is_editable && !$is_only_view && $can_edit && $task): ?>
        <button type="button" class="btn btn-sm btn-primary mt-2" onclick="editCustomIdentifier(this, 'i_<?= $identifier->id ?>', <?= $task->id ?>, <?= $identifier->id ?>)">Изменить</button>
    <?php endif; ?>

    <?php if ($status): ?>
        <div style="font-size: small; font-weight: bold; color: #a21f1f">
            <span style="color: red">*</span> <?= $status ?>
        </div>
    <?php endif; ?>

<?php else: ?>
    counterparty<?= $value->value_id ?>
<?php endif; ?>
