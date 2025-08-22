<?php

namespace app\modules\process\models\task_data;

use app\components\BaseActiveQuery;
use app\modules\process\models\_query\Req3TasksDataItemBasketsQuery;
use app\modules\process\models\identifiers\Req3Identifiers;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "req3_tasks_data_item_baskets".
 *
 * @property integer                           $id
 * @property integer                           $installment_type
 * @property integer                           $installment_value
 *
 * @property Req3TasksDataItemBasketServices[] $services
 * @property Req3TasksDataItems[]              $data_items
 * @property Req3TasksDataItemBasketLog[]      $history
 */
class Req3TasksDataItemBaskets extends ActiveRecord
{
    // ============================================================================
    // ============================== КОНСТАНТЫ ===================================
    // ============================================================================
    const INSTALLMENT_TYPE_MONTH_COUNT    = 1;
    const INSTALLMENT_TYPE_MONTHLY_AMOUNT = 2;

    const INSTALLMENT_TYPE_NAMES = [
        self::INSTALLMENT_TYPE_MONTH_COUNT    => "На количество месяцев",
        self::INSTALLMENT_TYPE_MONTHLY_AMOUNT => "На стоимость в месяц"
    ];

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
        return 'req3_tasks_data_item_baskets';
    }


    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'installment_type'  => 'Installment Type',
            'installment_value' => 'Installment Value',
        ];
    }


    public static function find()
    {
        return new Req3TasksDataItemBasketsQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================
    public function rules()
    {
        return [
            ['installment_value', 'validateValue', 'skipOnEmpty' => false],
            [['installment_type', 'installment_value'], 'integer'],
        ];
    }

    public function validateValue()
    {
        if ($this->installment_type !== null) {
            if (empty($this->installment_value)) {
                $this->addError('installment_value', 'Необходимо заполнить значение');
            } elseif ($this->installment_type == self::INSTALLMENT_TYPE_MONTH_COUNT) {
                if ($this->installment_value > 12) {
                    $this->addError('installment_value', 'Максимум 12 месяцев');
                }
            } elseif ($this->installment_type == self::INSTALLMENT_TYPE_MONTHLY_AMOUNT) {

                $sum = 0;
                foreach ($this->services as $basket_item) {
                    if (empty($basket_item->service_link_id) && ($basket_item->reward_service->utm_service ?? false)) {
                        $sum += $basket_item->price;
                    }else if (empty($basket_item->services2_link_id) && ($basket_item->reward_service->service2 ?? false)) {
                        $sum += $basket_item->price;
                    }
                }
                $month_count = ceil($sum / $this->installment_value);
                if ($month_count > 12) {
                    $this->addError('installment_value', "Максимум 12 месяцев, а по добавленным услугам выходит $month_count месяцев");
                }
            }
        } else {
            $this->installment_value = null;
        }
    }

    // ============================================================================
    // ============================== СЦЕНАРИИ ====================================
    // ============================================================================

    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
    public function getServices()
    {
        return $this->hasMany(Req3TasksDataItemBasketServices::class, ['basket_id' => 'id']);
    }

    /**
     * @return BaseActiveQuery
     */
    public function getData_items()
    {
        return $this->hasMany(Req3TasksDataItems::class, ['value_id' => 'id'])->type(Req3Identifiers::TYPE_SERVICE_BASKET);
    }

    /**
     * @return BaseActiveQuery
     */
    public function getHistory()
    {
        return $this->hasMany(Req3TasksDataItemBasketLog::class, ['basket_id' => 'id'])->orderBy(['date_add' => SORT_ASC, 'id' => SORT_ASC]);
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
    public function beforeDelete()
    {
        $items = $this->getServices()->all();
        foreach ($items as $item) $item->delete();

        return parent::beforeDelete();
    }

    // ============================================================================
    // ============================== ЧТО КАСАЕТСЯ OBJECT =========================
    // ============================================================================
    public function addToHistoryChangeData($change)
    {
        $item = new Req3TasksDataItemBasketLog();
        $item->basket_id = $this->id;
        $item->text_log = $change;
        $item->date_add = new Expression("NOW()");
        $item->save();
    }

    // ============================================================================
    // ============================== STATIC ======================================
    // ============================================================================
}