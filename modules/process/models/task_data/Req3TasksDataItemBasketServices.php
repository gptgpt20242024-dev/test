<?php

namespace app\modules\process\models\task_data;

use app\models\Opers;
use app\modules\process\models\_query\Req3TasksDataItemBasketServicesQuery;
use app\modules\process\models\rewards\Req3RewardServices;
use app\modules\service2\models\Services2Links;
use app\modules\service2\models\Services2LinksQuery;
use app\modules\utm\models\ServiceLinks;
use app\modules\utm\models\ServiceLinksQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "req3_tasks_data_item_basket_services".
 *
 * @property integer                  $id
 * @property integer                  $basket_id
 * @property integer                  $reward_service_id
 * @property integer                  $price
 * @property integer                  $oper_id
 * @property string                   $date_add
 * @property integer                  $order_task_id
 * @property integer                  $services2_link_id
 * @property integer                  $services2_link_oper_id
 * @property integer                  $service_link_id
 * @property integer                  $status
 * @property integer                  $is_deleted
 * @property integer                  $deleted_oper_id
 * @property string                   $date_deleted
 *
 * @property Req3RewardServices       $reward_service
 * @property Opers                    $oper
 * @property Services2Links           $services2_link
 * @property ServiceLinks           $service_link
 * @property Req3TasksDataItemBaskets $basket
 */
class Req3TasksDataItemBasketServices extends ActiveRecord
{
    // ============================================================================
    // ============================== КОНСТАНТЫ ===================================
    // ============================================================================
    const STATUS_DEFAULT = 0;

    // ============================================================================
    // ============================== ДОПОЛНИТЕЛЬНЫЕ ПОЛЯ =========================
    // ============================================================================

    // ============================================================================
    // ============================== ИНИТ ========================================
    // ============================================================================
    public function init()
    {
    }

    public static function tableName()
    {
        return 'req3_tasks_data_item_basket_services';
    }


    public function attributeLabels()
    {
        return [
            'id'                     => 'ID',
            'basket_id'              => 'Basket ID',
            'reward_service_id'      => 'Reward Service ID',
            'price'                  => 'Price',
            'oper_id'                => 'Oper ID',
            'date_add'               => 'Date Add',
            'order_task_id'          => 'Order Task ID',
            'services2_link_id'      => 'Services2 Link ID',
            'services2_link_oper_id' => 'Сотрудник который привязал к абоненту',
            'service_link_id'      => 'UTM Services Link ID',
            'status'                 => 'Status',
            'is_deleted'             => 'Is Deleted',
            'deleted_oper_id'        => 'Deleted Oper ID',
            'date_deleted'           => 'Date Deleted',
        ];
    }


    public static function find($only_active = true)
    {
        $query = new Req3TasksDataItemBasketServicesQuery(get_called_class());
        if ($only_active) $query->active();
        return $query;
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => self::STATUS_DEFAULT],
            [['is_deleted'], 'default', 'value' => 0],

            [['basket_id', 'reward_service_id', 'price', 'date_add', 'status'], 'required'],
            [['basket_id', 'reward_service_id', 'price', 'oper_id', 'order_task_id', 'services2_link_id', 'services2_link_oper_id', 'service_link_id', 'status', 'is_deleted', 'deleted_oper_id'], 'integer'],
            [['date_add', 'date_deleted'], 'safe'],
        ];
    }

    // ============================================================================
    // ============================== СЦЕНАРИИ ====================================
    // ============================================================================

    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
    public function getReward_service()
    {
        return $this->hasOne(Req3RewardServices::class, ['id' => 'reward_service_id']);
    }

    public function getOper()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'oper_id']);
    }

    public function getServices2_link()
    {
        /** @var Services2LinksQuery $query */
        $query = $this->hasOne(Services2Links::class, ['id' => 'services2_link_id']);
        return $query->removeActive();
    }

    public function getService_link()
    {
        /** @var ServiceLinksQuery $query */
        $query = $this->hasOne(ServiceLinks::class, ['id' => 'service_link_id']);
        return $query->removeActive();
    }

    public function getBasket()
    {
        return $this->hasOne(Req3TasksDataItemBaskets::class, ['id' => 'basket_id']);
    }

    // ============================================================================
    // ============================== СЕТТЕРЫ =====================================
    // ============================================================================

    // ============================================================================
    // ============================== СОБЫТИЯ СОХРАНЕНИЯ ==========================
    // ============================================================================

    // ============================================================================
    // ============================== СОБЫТИЯ УДАЛЕНИЯ ============================
    // ============================================================================

    // ============================================================================
    // ============================== ЧТО КАСАЕТСЯ OBJECT =========================
    // ============================================================================

    // ============================================================================
    // ============================== STATIC ======================================
    // ============================================================================
}