<?php

namespace app\modules\process\models\chats;

use app\models\Opers;
use app\modules\process\models\_query\Req3TasksChatMessagesQuery;
use app\modules\scheduler\components\HelperThread;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "req3_tasks_chat_messages".
 *
 * @property integer                     $id
 * @property integer                     $chat_id
 * @property string                      $message
 * @property integer                     $type
 * @property integer                     $oper_id
 * @property string                      $date_add
 *
 * @property Req3TasksChats              $chat
 * @property Opers                       $oper
 * @property Req3TasksChatMessageFiles[] $files
 */
class Req3TasksChatMessages extends ActiveRecord
{
    // ============================================================================
    // ============================== КОНСТАНТЫ ===================================
    // ============================================================================
    const TYPE_OPER = 1;
    const TYPE_INFO = 2;

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
        return 'req3_tasks_chat_messages';
    }

    public function attributeLabels()
    {
        return [
            'id'       => 'ID',
            'chat_id'  => 'Chat ID',
            'message'  => 'текст сообщения',
            'type'     => 'Type',
            'oper_id'  => 'Oper ID',
            'date_add' => 'Date Add',
        ];
    }

    public static function find()
    {
        return new Req3TasksChatMessagesQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================
    const SCENARIO_IGNORE_REPEAT = 'SCENARIO_IGNORE_REPEAT';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_IGNORE_REPEAT] = $scenarios[self::SCENARIO_DEFAULT];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['chat_id', 'message', 'type', 'oper_id', 'date_add'], 'required'],
            [['chat_id', 'type', 'oper_id'], 'integer'],
            [['message'], 'string'],
            [['date_add'], 'safe'],
            ['message', 'validateMessage', 'on' => self::SCENARIO_DEFAULT]
        ];
    }

    public function validateMessage()
    {
        if (!empty($this->chat_id)) {
            $last_message = Req3TasksChatMessages::find()->andWhere(['chat_id' => $this->chat_id])->orderBy(['date_add' => SORT_DESC])->limit(1)->one();
            if ($last_message) {
                if ($last_message->oper_id == $this->oper_id) {
                    if ($last_message->message == $this->message && time() - strtotime($last_message->date_add) < 10) {
                        $this->addError('message', "Повтор сообщения за короткое время.");
                    }
                }
            }
        }
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

    public function getFiles()
    {
        return $this->hasMany(Req3TasksChatMessageFiles::class, ['message_id' => 'id'])->inverseOf('message');
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
                $this->chat->last_message_id = $this->id;
                $this->chat->date_last_message = $this->date_add;
                $this->chat->save(true, ['last_message_id', 'date_last_message']);

                /** @see Req3TasksChats::sendTelegram() */
                HelperThread::startMethod(2, $this->chat, "sendTelegram", $this->id);
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    // ============================================================================
    // ============================== СОБЫТИЯ УДАЛЕНИЯ ============================
    // ============================================================================
    public function beforeDelete()
    {
        Req3TasksChatMessageFiles::deleteAll(['message_id' => $this->id]);
        return parent::beforeDelete();
    }

    public static function deleteByChatIds(array $chatIds)
    {
        $messageIds = self::find()->andWhere(['chat_id' => $chatIds])->select('id')->column();
        if (!empty($messageIds)) {
            Req3TasksChatMessageFiles::deleteAll(['message_id' => $messageIds]);
            self::deleteAll(['id' => $messageIds]);
        }
    }
    // ============================================================================
    // ============================== ЧТО КАСАЕТСЯ OBJECT =========================
    // ============================================================================

    // ============================================================================
    // ============================== STATIC ======================================
    // ============================================================================
}