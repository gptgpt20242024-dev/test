<?php

namespace app\modules\process\models\task_data;

use app\modules\counterparties\models\CounterpartiesPhysFace;
use app\modules\counterparties\models\DocPassportRf;
use app\modules\counterparties\models\DocResidence;
use app\modules\counterparties\models\DocRwp;
use app\modules\process\models\_query;
use app\modules\process\models\identifiers\Req3Identifiers;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "req3_tasks_data_item_docs".
 *
 * @property integer              $id
 * @property integer              $doc_type
 * @property integer              $doc_id
 *
 * @property DocPassportRf        $doc_passport_rf
 * @property DocRwp               $doc_rwp
 * @property DocResidence         $doc_residence
 * @property Req3TasksDataItems[] $data_items
 */
class Req3TasksDataItemDocs extends ActiveRecord
{
    const DOC_TYPE_PASSPORT_RF = CounterpartiesPhysFace::DOC_TYPE_PASSPORT_RF;
    const DOC_TYPE_RWP         = CounterpartiesPhysFace::DOC_TYPE_RWP;
    const DOC_TYPE_RESIDENCE   = CounterpartiesPhysFace::DOC_TYPE_RESIDENCE;
    const DOC_TYPE_NONE        = CounterpartiesPhysFace::DOC_TYPE_NONE;

    const DOC_TYPE_NAMES = CounterpartiesPhysFace::DOC_TYPE_NAMES;

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
        return 'req3_tasks_data_item_docs';
    }


    public function attributeLabels()
    {
        return [
            'id'       => 'ID',
            'doc_type' => 'Тип документа',
            'doc_id'   => 'Doc ID',
        ];
    }

    public static function find()
    {
        return new _query\Req3TasksDataItemDocsQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================
    public function rules()
    {
        return [
            [['doc_type'], 'required'],
            [['doc_type', 'doc_id'], 'integer'],
        ];
    }

    // ============================================================================
    // ============================== СЦЕНАРИИ ====================================
    // ============================================================================

    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
    public function getDoc_passport_rf()
    {
        if (count($this->fields()) == 0 || $this->doc_type == self::DOC_TYPE_PASSPORT_RF) return $this->hasOne(DocPassportRf::class, ['id' => 'doc_id']);
        else return null;
    }

    public function getDoc_rwp()
    {
        if (count($this->fields()) == 0 || $this->doc_type == self::DOC_TYPE_RWP) return $this->hasOne(DocRwp::class, ['id' => 'doc_id']);
        else return null;
    }

    public function getDoc_residence()
    {
        if (count($this->fields()) == 0 || $this->doc_type == self::DOC_TYPE_RESIDENCE) return $this->hasOne(DocResidence::class, ['id' => 'doc_id']);
        else return null;
    }

    public function getData_items()
    {
        return $this->hasMany(Req3TasksDataItems::class, ['value_id' => 'id'])->andOnCondition(['type' => Req3Identifiers::TYPE_COUNTERPARTIES_DOCUMENTS]);
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
    /**
     * @param Req3TasksDataItems|null $item
     * @return bool
     */
    public function isAccessEdit(Req3TasksDataItems $item = null): bool
    {
        if (empty($this->doc_id)) return true;

        //привязана к физ лицу, то менять нельзя
        if ($this->doc_type == self::DOC_TYPE_PASSPORT_RF && ($this->doc_passport_rf->phys_face ?? false)) {
            return false;
        }
        if ($this->doc_type == self::DOC_TYPE_RWP && ($this->doc_rwp->phys_face ?? false)) {
            return false;
        }
        if ($this->doc_type == self::DOC_TYPE_RESIDENCE && ($this->doc_residence->phys_face ?? false)) {
            return false;
        }

        //если этот документ привязан только к нам, то можно
        $query = self::find()
            ->joinWith('data_items')
            ->andWhere([Req3TasksDataItems::tableName() . '.is_deleted' => 0])
            ->andWhere([self::tableName() . '.doc_type' => $this->doc_type, self::tableName() . '.doc_id' => $this->doc_id])
            ->andFilterWhere(['!=', self::tableName() . '.id', $this->id]);

        if ($item) {
            $query->andFilterWhere([
                'AND',
                ["!=", Req3TasksDataItems::tableName() . '.link_id', $item->link_id],
                ["!=", Req3TasksDataItems::tableName() . '.link_type', $item->link_type]
            ]);
        }

        return !$query->exists();
    }

    public function getTitle()
    {
        if ($this->doc_type == self::DOC_TYPE_PASSPORT_RF && $this->doc_passport_rf) {
            return "Паспорт РФ: {$this->doc_passport_rf->getTitle()}";
        }
        if ($this->doc_type == self::DOC_TYPE_RWP && $this->doc_rwp) {
            return "РВП: {$this->doc_rwp->getTitle()}";
        }
        if ($this->doc_type == self::DOC_TYPE_RESIDENCE && $this->doc_residence) {
            return "Вид на жительство: {$this->doc_residence->getTitle()}";
        }
        if ($this->doc_type == self::DOC_TYPE_NONE) {
            return "Нет документа";
        }
        return "-";
    }

    public function getTitleSimple()
    {
        if ($this->doc_type == self::DOC_TYPE_PASSPORT_RF && $this->doc_passport_rf) {
            return "Паспорт РФ";
        }
        if ($this->doc_type == self::DOC_TYPE_RWP && $this->doc_rwp) {
            return "РВП";
        }
        if ($this->doc_type == self::DOC_TYPE_RESIDENCE && $this->doc_residence) {
            return "Вид на жительство";
        }
        if ($this->doc_type == self::DOC_TYPE_NONE) {
            return "Нет документа";
        }
        return "-";
    }

    // ============================================================================
    // ============================== STATIC ======================================
    // ============================================================================
}
