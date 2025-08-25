<?php

namespace app\modules\process\models\identifiers;

use app\models\ActiveRecordCache;
use app\modules\process\models\_query;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\models\template\Req3TemplateVersions;

/**
 * This is the model class for table "req3_identifier_orders".
 *
 * @property integer              $id
 * @property integer              $identifier_id
 * @property integer              $version_id
 * @property integer              $number
 *
 * @property Req3Identifiers      $identifier
 * @property Req3TemplateVersions $version
 */
class Req3IdentifierOrders extends ActiveRecordCache
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
        return 'req3_identifier_orders';
    }


    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'identifier_id' => 'Identifier ID',
            'version_id'    => 'Version ID',
            'number'        => 'Number',
        ];
    }


    public static function find()
    {
        return new _query\Req3IdentifierOrdersQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================
    public function rules()
    {
        return [
            [['identifier_id', 'version_id', 'number'], 'required'],
            [['identifier_id', 'version_id', 'number'], 'integer'],
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
    public function getIdentifier()
    {
        return $this->hasOne(Req3Identifiers::class, ['id' => 'identifier_id']);
    }

    public function getVersion()
    {
        return $this->hasOne(Req3TemplateVersions::class, ['id' => 'version_id']);
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
    /**
     * @param                   $version_id
     * @param Req3Identifiers[] $identifiers
     */
    public static function sort($version_id, &$identifiers)
    {
        $orders = Req3IdentifierOrders::getByVersionId($version_id);

        $fnct_get_id = function ($item) {
            if ($item instanceof Req3Identifiers) return $item->id;
            if ($item instanceof Req3TasksDataItems) return $item->identifier_id;
            return 0;
        };

        $fnct_get_number = function ($item) use (&$fnct_get_id, &$orders) {
            $id = $fnct_get_id($item);
            if (isset($orders[$id])) return $orders[$id];
            return 0;
        };

        uasort($identifiers, function ($a, $b) use (&$fnct_get_number) {
            $n1 = $fnct_get_number($a);
            $n2 = $fnct_get_number($b);
            return $n1 <=> $n2;
        });
    }

    /**
     * @param Req3IdentifierOrders[]     $sorting
     * @param                            $to_version_id
     * @param                            $map_identifiers
     */
    public static function copyTo($sorting, $to_version_id, $map_identifiers)
    {
        //переносим сортировку с шаблоном
        foreach ($sorting as $sort) {
            if (isset($map_identifiers[$sort->identifier_id])) {
                $new_sort = new Req3IdentifierOrders();
                $new_sort->load($sort->attributes, '');

                $new_sort->version_id = $to_version_id;
                $new_sort->identifier_id = $map_identifiers[$sort->identifier_id];

                $new_sort->save();
            }
        }
    }

    public static function getByVersionId($version_id)
    {
        return self::getStatic(function () use ($version_id) {
            return Req3IdentifierOrders::find()->versionId($version_id)->select('number')->indexBy('identifier_id')->column();
        });
    }
}
