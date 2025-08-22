<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */
?>

<?php
$xml = '';
if ($value->type == Req3Identifiers::TYPE_TEXT) $xml = "_text";
if ($value->type == Req3Identifiers::TYPE_TEXT_ADDRESS) $xml = "_text_address";
if ($value->type == Req3Identifiers::TYPE_TEXT_PHONE) $xml = "_text_phone";
if ($value->type == Req3Identifiers::TYPE_TEXT_DATE) $xml = "_text_date";
if ($value->type == Req3Identifiers::TYPE_TEXT_DATE_TIME) $xml = "_text_date_time";
if ($value->type == Req3Identifiers::TYPE_NUMBER) $xml = "_number";
if ($value->type == Req3Identifiers::TYPE_USER) $xml = "_user";
if ($value->type == Req3Identifiers::TYPE_USER_TYPE) $xml = "_user_type";
if ($value->type == Req3Identifiers::TYPE_USER_BLOCK_REASON) $xml = "_user_block_reason";
if ($value->type == Req3Identifiers::TYPE_USER_CREDIT) $xml = "_user_credit";
if ($value->type == Req3Identifiers::TYPE_USER_KNOWN_FROM) $xml = "_user_known_from";
if ($value->type == Req3Identifiers::TYPE_USER_CONNECT_FROM) $xml = "_user_connect_from";
if ($value->type == Req3Identifiers::TYPE_USER_TYPE_CONNECT) $xml = "_user_type_connect";
if ($value->type == Req3Identifiers::TYPE_ADDRESS) $xml = "_address";
if ($value->type == Req3Identifiers::TYPE_ADDRESS_CAP) $xml = "_address_cap";
if ($value->type == Req3Identifiers::TYPE_ADDRESS_ADD) $xml = "_address_add";
if ($value->type == Req3Identifiers::TYPE_ADDRESS_ANY) $xml = "_address_any";
if ($value->type == Req3Identifiers::TYPE_ADDRESS_TREE_COVERAGE) $xml = "_address_tree_coverage";
if ($value->type == Req3Identifiers::TYPE_ADDRESS_SETTING_LABEL) $xml = "_address_setting_label";
if ($value->type == Req3Identifiers::TYPE_FIN_MANAGER) $xml = "_fm";
if ($value->type == Req3Identifiers::TYPE_UTM_TARIFF) $xml = "_tariff";
if ($value->type == Req3Identifiers::TYPE_UTM_SERVICE) $xml = "_service";
if ($value->type == Req3Identifiers::TYPE_UTM_DP) $xml = "_dp";
if ($value->type == Req3Identifiers::TYPE_UTM_DP_MONTH_AUTO) $xml = "_dp_month_auto";
if ($value->type == Req3Identifiers::TYPE_REWARD_SERVICE) $xml = "_reward_service";
if ($value->type == Req3Identifiers::TYPE_WH_ITEM) $xml = "_item";
if ($value->type == Req3Identifiers::TYPE_WH_ITEM_SIMPLE) $xml = "_item_simple";
if ($value->type == Req3Identifiers::TYPE_WH_SYN_ITEM) $xml = "_syn_item";
if ($value->type == Req3Identifiers::TYPE_WH_WAREHOUSE) $xml = "_warehouse";
if ($value->type == Req3Identifiers::TYPE_WH_TEMPLATE) $xml = "_wh_template";
if ($value->type == Req3Identifiers::TYPE_WH_BALANCE) $xml = "_wh_balance";
if ($value->type == Req3Identifiers::TYPE_OPER) $xml = "_oper";
if ($value->type == Req3Identifiers::TYPE_OPER_ROLE) $xml = "_oper_role";
if ($value->type == Req3Identifiers::TYPE_FILE_1) $xml = "_file";
if ($value->type == Req3Identifiers::TYPE_LIST) $xml = "_list";
if ($value->type == Req3Identifiers::TYPE_LIST_TREE) $xml = "_list_tree";
if ($value->type == Req3Identifiers::TYPE_GROUP) $xml = "_group";
if ($value->type == Req3Identifiers::TYPE_ROLE) $xml = "_role";
if ($value->type == Req3Identifiers::TYPE_COUNTERPARTIES) $xml = "_counterparty";
if ($value->type == Req3Identifiers::TYPE_COUNTERPARTIES_DOCUMENTS) $xml = "_counterparty_documents";
if ($value->type == Req3Identifiers::TYPE_COUNTERPARTIES_DIADOK_DOCUMENT_STATUS) $xml = "_counterparties_diadok_status";
if ($value->type == Req3Identifiers::TYPE_COUNTERPARTIES_DIADOK_DOCUMENT_TYPE) $xml = "_counterparties_diadok_type";
if ($value->type == Req3Identifiers::TYPE_CALL_STATUS) $xml = "_call_status";
if ($value->type == Req3Identifiers::TYPE_CALL_NAMES) $xml = "_call_name";
if ($value->type == Req3Identifiers::TYPE_DOCUMENT_TYPE) $xml = "_doc_type";
if ($value->type == Req3Identifiers::TYPE_STATUS_ZONE) $xml = "_status_zone";
if ($value->type == Req3Identifiers::TYPE_TEMPLATES) $xml = "_templates";
if ($value->type == Req3Identifiers::TYPE_TEMPLATE_STEPS) $xml = "_template_steps";
if ($value->type == Req3Identifiers::TYPE_SERVICE_BASKET) $xml = "_service_basket";
if ($value->type == Req3Identifiers::TYPE_CHECK_WORK_RATER_CONFIRMATION) $xml = "_check_work_rater_confirmation";
if ($value->type == Req3Identifiers::TYPE_REWARDS) $xml = "_rewards";
if ($value->type == Req3Identifiers::TYPE_VFP_LIST) $xml = "_vfp";
if ($value->type == Req3Identifiers::TYPE_QUEUE_LABEL) $xml = "_queue_label";
if ($value->type == Req3Identifiers::TYPE_BONUS_REWARD) $xml = "_bonus_reward";
if ($value->type == Req3Identifiers::TYPE_COUNTERPARTIES_ARCHIVE_DOC) $xml = "_counterparties_archive_doc";
if ($value->type == Req3Identifiers::TYPE_COMMUNICATION_CHANNELS) $xml = "_communication_channels";
if ($value->type == Req3Identifiers::TYPE_WORK_RATER) $xml = "_work_rater";
if ($value->type == Req3Identifiers::TYPE_CRASH_REASONS) $xml = "_crash_reasons";
if ($value->type == Req3Identifiers::TYPE_ORDER) $xml = "_order";
if ($value->type == Req3Identifiers::TYPE_KTV_DEVICE) $xml = "_ktv_device";
if ($value->type == Req3Identifiers::TYPE_SWITCH_PORT) $xml = "_device";
if ($value->type == Req3Identifiers::TYPE_ACS_DEVICE) $xml = "_acs_device";
if ($value->type == Req3Identifiers::TYPE_ACS_INTERCOM_TYPE) $xml = "_acs_intercom_type";

if ($value->type == Req3Identifiers::TYPE_TEMP_EXECUTOR) $xml = "_executor";
if ($value->type == Req3Identifiers::TYPE_TEMP_CONTROLLER) $xml = "_controller";
if ($value->type == Req3Identifiers::TYPE_TEMP_OBSERVER) $xml = "_observers";
?>
<?php if (!empty($xml)): ?>
    <?= $this->render("types/{$xml}", ['task' => $task, 'identifier' => $identifier, 'value' => $value]) ?>
<?php endif; ?>