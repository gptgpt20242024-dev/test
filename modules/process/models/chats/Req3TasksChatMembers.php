<?php

namespace app\modules\process\models\chats;

use app\models\Opers;
use app\modules\lists\models\ListsItems;
use app\modules\process\components\HelperOper;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "req3_tasks_chat_members".
 *
 * @property integer        $id
 * @property integer        $chat_id
 * @property integer        $oper_id
 * @property integer        $is_executor
 * @property integer        $is_controller
 * @property integer        $invited_id
 * @property string         $date_add
 * @property string         $date_left
 * @property integer        $left_item_id
 * @property string         $date_last_see
 *
 * @property Req3TasksChats $chat
 * @property Opers          $oper
 * @property Opers          $invited
 * @property ListsItems     $left_item
 */
class Req3TasksChatMembers extends ActiveRecord
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
        return 'req3_tasks_chat_members';
    }


    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'chat_id'       => 'Chat ID',
            'oper_id'       => 'Oper ID',
            'is_executor' => 'is_executor',
            'is_controller' => 'Is Controller',
            'invited_id'    => 'Invited ID',
            'date_add'      => 'Date Add',
            'date_left'     => 'Date Left',
            'left_item_id'  => 'Left Item ID',
            'date_last_see' => 'date_last_see',
        ];
    }


    public static function find()
    {
        return new \app\modules\process\models\_query\Req3TasksChatMembersQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================
    public function rules()
    {
        return [
            [['chat_id', 'oper_id', 'date_add'], 'required'],
            [['chat_id', 'oper_id', 'is_executor', 'is_controller', 'invited_id', 'left_item_id'], 'integer'],
            [['date_add', 'date_left', 'date_last_see'], 'safe'],

            [['left_item_id'], 'required', 'when' => function ($model) {
                return !empty($model->date_left);
            }],
        ];
    }

    // ============================================================================
    // ============================== СЦЕНАРИИ ====================================
    // ============================================================================

    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
    public function getChat()
    {
        return $this->hasOne(Req3TasksChats::class, ['id' => 'chat_id']);
    }

    public function getOper()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'oper_id']);
    }

    public function getInvited()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'invited_id']);
    }

    public function getLeft_item()
    {
        return $this->hasOne(ListsItems::class, ['id' => 'left_item_id']);
    }

    // ============================================================================
    // ============================== СЕТТЕРЫ =====================================
    // ============================================================================

    // ============================================================================
    // ============================== СОБЫТИЯ СОХРАНЕНИЯ ==========================
    // ============================================================================
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            if ($this->chat) {
                if ($this->chat->creator_id != $this->oper_id) {

                    if (!empty($this->invited_id)) {
                        $text = HelperOper::getFio($this, 'invited_id', 'invited') .
                            " добавил(-а) в чат " .
                            HelperOper::getFio($this, 'oper_id', 'oper');
                    } else {
                        $text = Opers::getFioOrFioDeletedHtml($this, 'oper', 'oper_id') . " вступил(-а) в чат";
                    }

                    if ($this->is_controller == 1) {
                        $text .= " (* ответственный(-ая) по задаче)";
                    }

                    if ($this->is_executor == 1) {
                        $text .= " (* исполнитель по задаче)";
                    }

                    $this->chat->addMessageInfo($text);
                }
            }
        } else {
            if (array_key_exists('date_left', $changedAttributes) && $this->date_left != $changedAttributes['date_left']) {
                if ($this->date_left != null) {
                    $text = Opers::getFioOrFioDeletedHtml($this) . " покинул(-а) чат (" . ($this->left_item->value ?? "-") . ")";
                    $this->chat->addMessageInfo($text);
                } else {
                    $text = Opers::getFioOrFioDeletedHtml($this) . " вернулся(-ась) в чат";
                    $this->chat->addMessageInfo($text);
                }
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

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