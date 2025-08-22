<?php

namespace app\modules\process\models\task_data;

use app\modules\address\models\Locations;
use app\modules\address\models\MapAddresses;
use app\modules\address\models\MapHouses;
use app\modules\address\models\MapStreets;
use app\modules\process\models\_query;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "req3_tasks_data_item_address".
 *
 * @property integer      $id
 * @property integer      $item_id
 * @property integer      $location_id
 * @property integer      $street_id
 * @property integer      $house_id
 * @property integer      $address_id
 * @property double       $coverage
 *
 * @property Locations    $location
 * @property MapStreets   $street
 * @property MapHouses    $house
 * @property MapAddresses $address
 */
class Req3TasksDataItemAddress extends ActiveRecord
{
    // ============================================================================
    // ============================== КОНСТАНТЫ ===================================
    // ============================================================================

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
        return 'req3_tasks_data_item_address';
    }


    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'item_id'     => 'Item ID',
            'location_id' => 'Location ID',
            'street_id'   => 'Street ID',
            'house_id'    => 'House ID',
            'address_id'  => 'Address ID',
            'coverage'    => 'coverage',
        ];
    }


    public static function find()
    {
        return new _query\Req3TasksDataItemAddressQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================
    public function rules()
    {
        return [
            [['item_id'], 'required'],
            [['item_id', 'location_id', 'street_id', 'house_id', 'address_id'], 'integer'],
            [['coverage'], 'number'],
        ];
    }

    // ============================================================================
    // ============================== СЦЕНАРИИ ====================================
    // ============================================================================
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return $scenarios;
    }


    public function transactions()
    {
        return [
            //self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
    public function getLocation()
    {
        if (count($this->fields()) == 0 || $this->location_id != null) return $this->hasOne(Locations::class, ['id' => 'location_id']);
        else return null;
    }

    public function getStreet()
    {
        if (count($this->fields()) == 0 || $this->street_id != null) return $this->hasOne(MapStreets::class, ['street_id' => 'street_id']);
        else return null;
    }

    public function getHouse()
    {
        if (count($this->fields()) == 0 || $this->house_id != null) return $this->hasOne(MapHouses::class, ['house_id' => 'house_id']);
        else return null;
    }

    public function getAddress()
    {
        if (count($this->fields()) == 0 || $this->address_id != null) return $this->hasOne(MapAddresses::class, ['addr_id' => 'address_id']);
        else return null;
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
    public function getAddressModel()
    {
        if ($this->address_id != null && $this->address) return $this->address;
        if ($this->house_id != null && $this->house) return $this->house;
        if ($this->street_id != null && $this->street) return $this->street;
        if ($this->location_id != null && $this->location) return $this->location;
        return null;
    }

    /** address.house.street.location.parent.parent.parent.parent.parent */
    /** house.street.location.parent.parent.parent.parent.parent */
    /** street.location.parent.parent.parent.parent.parent */
    /** location.parent.parent.parent.parent.parent */
    public function getFullName($ignore_lvl = null, $invert = false, $separate_brackets = false, $forceFlat = false)
    {
        $address = $this->getAddressModel();
        if ($address) {
            if ($address instanceof MapAddresses) return $address->getFullName($ignore_lvl, $invert, $separate_brackets, $forceFlat);
            if ($address instanceof MapHouses) return $address->getFullName($ignore_lvl, $invert, $separate_brackets);
            if ($address instanceof MapStreets) return $address->getFullName($ignore_lvl, $invert, $separate_brackets);
            if ($address instanceof Locations) return $address->getFullName($ignore_lvl, $invert);
        }

        return "";
    }

    // ============================================================================
    // ============================== STATIC ======================================
    // ============================================================================
}
