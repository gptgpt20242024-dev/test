<?php

namespace app\modules\process\models\chats;

use app\components\CacheTrait;
use app\components\Date;
use app\components\Str;
use app\models\Opers;
use app\modules\bot\modules\telegram\components\HelperTelegram;
use app\modules\lists\models\ListsItems;
use app\modules\process\components\HelperOper;
use app\modules\process\models\_query\Req3TasksChatsQuery;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_opers\Req3TaskOperStepOther;
use app\modules\process\services\ProcessTelegramService;
use app\modules\scheduler\models\Scheduler;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "req3_tasks_chats".
 *
 * @property integer                 $id
 * @property integer                 $task_id
 * @property string                  $topic
 * @property integer                 $is_active
 * @property integer                 $creator_id
 * @property integer                 $close_id
 * @property integer                 $close_item_id
 * @property string                  $date_add
 * @property string                  $date_close
 * @property string                  $date_last_message
 * @property integer                 $last_message_id
 *
 * @property Opers                   $creator
 * @property Opers                   $close
 * @property Req3Tasks               $task
 * @property Req3TasksChatMembers[]  $members
 * @property Req3TasksChatMessages[] $messages
 * @property ListsItems              $close_item
 */
class Req3TasksChats extends ActiveRecord
{
    use CacheTrait;

    // ============================================================================
    // ============================== КОНСТАНТЫ ===================================
    // ============================================================================
    const CLOSE_ITEM_TIMEOUT = -1;

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
        return 'req3_tasks_chats';
    }


    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'task_id'           => 'Task ID',
            'topic'             => 'Topic',
            'is_active'         => 'Is Active',
            'creator_id'        => 'Creator ID',
            'close_id'          => 'Close ID',
            'close_item_id'     => 'Close Item ID',
            'date_add'          => 'Date Add',
            'date_close'        => 'Date Close',
            'date_last_message' => 'Date Last Message',
            'last_message_id'   => 'Last Message ID',
        ];
    }


    public static function find()
    {
        return new Req3TasksChatsQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================
    public function rules()
    {
        return [
            [['task_id', 'topic', 'creator_id', 'date_add', 'date_last_message', 'last_message_id'], 'required'],
            [['task_id', 'is_active', 'creator_id', 'close_id', 'close_item_id', 'last_message_id'], 'integer'],
            [['date_add', 'date_close', 'date_last_message'], 'safe'],
            [['topic'], 'string', 'max' => 256],

            [['close_item_id'], 'required', 'when' => function ($model) {
                return $model->is_active == 0;
            }],
        ];
    }

    // ============================================================================
    // ============================== СЦЕНАРИИ ====================================
    // ============================================================================

    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
    public function getCreator()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'creator_id']);
    }

    public function getClose()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'close_id']);
    }

    public function getTask()
    {
        return $this->hasOne(Req3Tasks::class, ['id' => 'task_id']);
    }

    public function getMembers()
    {
        return $this->hasMany(Req3TasksChatMembers::class, ['chat_id' => 'id'])->inverseOf('chat');
    }

    public function getMessages()
    {
        return $this->hasMany(Req3TasksChatMessages::class, ['chat_id' => 'id'])->orderBy(['date_add' => SORT_ASC])->inverseOf('chat');
    }

    public function getClose_item()
    {
        return $this->hasOne(ListsItems::class, ['id' => 'close_item_id']);
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
            /** @see Req3TasksChats::$creator */
            /** @see Req3TasksChats::$creator_id */
            $text = HelperOper::getFio($this, 'creator_id', 'creator') . " создал чат.";
            $this->addMessageInfo($text);
        } else {
            if (isset($changedAttributes['is_active'])) {
                $old_is_active = $changedAttributes['is_active'];
                if ($old_is_active == 1 && $this->is_active == 0) {
                    /** @see Req3TasksChats::$close */
                    /** @see Req3TasksChats::$close_id */
                    $text = HelperOper::getFio($this, 'close_id', 'close') . " закрыл чат (" . ($this->getCloseItemValue()) . ").";
                    $this->addMessageInfo($text);
                    $this->task->initQueueLabel(true);
                }
            }
            if (isset($changedAttributes['date_last_message'])) {
                if ($this->is_active == 1) {
                    $date = new Date();
                    $date->addMinutes(60);
                    (new Scheduler())->object($this, 'checkDateLastMessage')->deleteSameCommands()->date($date)->save();
                }
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    // ============================================================================
    // ============================== СОБЫТИЯ УДАЛЕНИЯ ============================
    // ============================================================================
    public function beforeDelete()
    {
        Req3TasksChatMembers::deleteAll(['chat_id' => $this->id]);

        Req3TasksChatTelegramSend::deleteAll(['link_id' => $this->id]);

        $items = $this->getMessages()->all();
        foreach ($items as $item) $item->delete();

        return parent::beforeDelete();
    }

    public static function deleteAllByTaskId(int $taskId)
    {
        $ids = self::find()->where(['task_id' => $taskId])->select('id')->column();
        if (!empty($ids)) {
            Req3TasksChatMembers::deleteAll(['chat_id' => $ids]);
            Req3TasksChatTelegramSend::deleteAll(['link_id' => $ids]);
            Req3TasksChatMessages::deleteByChatIds($ids);
            self::deleteAll(['id' => $ids]);
        }
    }


    // ============================================================================
    // ============================== ЧТО КАСАЕТСЯ OBJECT =========================
    // ============================================================================
    /**
     * @param                $oper_id
     * @param                $text
     * @param UploadedFile[] $files
     * @return void
     * @throws Exception
     */
    public function addMessage($oper_id, $text, $files = [], $validateRepeat = true)
    {
        if (empty($text) && count($files) > 0) $text = "Пустое сообщение с файлами (" . count($files) . ")";

        $message = new Req3TasksChatMessages();
        $message->chat_id = $this->id;
        $message->populateRelation('chat', $this);
        $message->message = $text;
        $message->type = Req3TasksChatMessages::TYPE_OPER;
        $message->oper_id = $oper_id;
        $message->date_add = new Expression("NOW()");
        if (!$validateRepeat) {
            $message->scenario = Req3TasksChatMessages::SCENARIO_IGNORE_REPEAT;
        }
        if (!$message->save()) {
            throw new Exception("Ошибка добавления сообщения: " . implode(", ", $message->getFirstErrors()));
        }

        if (count($files) > 0) {
            foreach ($files as $file) {
                $directory = Yii::getAlias("@app/modules/process/files/chats/");
                $path = "task_{$this->task_id}/";
                $prefix = "file_{$message->id}_" . date("Ymd_His") . "_";
                do {
                    $name = $prefix;
                    $name .= Str::generateRandomString(3);
                    $name .= "." . $file->extension;
                } while (file_exists($directory . $path . $name));

                if (!is_dir($directory . $path)) mkdir($directory . $path, 0777, true);

                if ($file->saveAs($directory . $path . $name, false)) {
                    $file_new = new Req3TasksChatMessageFiles();
                    $file_new->message_id = $message->id;
                    $file_new->orig_name = $file->name;
                    $file_new->save_name = $name;
                    $file_new->path = $path;
                    $file_new->save();
                }
            }
        }
    }

    public function addMessageInfo($text)
    {
        $message = new Req3TasksChatMessages();
        $message->chat_id = $this->id;
        $message->populateRelation('chat', $this);
        $message->message = $text;
        $message->type = Req3TasksChatMessages::TYPE_INFO;
        $message->oper_id = HelperOper::CREATOR_SYSTEM;
        $message->date_add = new Expression("NOW()");
        if (!$message->save()) {
            throw new Exception("Ошибка добавления информационного сообщения: " . implode(", ", $message->getFirstErrors()));
        }
    }

    public function addMember($oper_id, $invited_id = null)
    {
        $controllers = ArrayHelper::index($this->task->controllers ?? [], 'oper_id');
        $executors = ArrayHelper::index($this->task->executors ?? [], 'oper_id');

        /** @var Req3TasksChatMembers[] $members */
        $members = ArrayHelper::index($this->members, 'oper_id');
        if (isset($members[$oper_id])) {
            $members[$oper_id]->left_item_id = null;
            $members[$oper_id]->date_left = null;
            if (!$members[$oper_id]->save(true, ['left_item_id', 'date_left'])) {
                throw new Exception("Ошибка возврата участника: " . implode(", ", $members[$oper_id]->getFirstErrors()));
            }
        } else {
            $member = new Req3TasksChatMembers();
            $member->chat_id = $this->id;
            $member->populateRelation('chat', $this);
            $member->oper_id = $oper_id;
            $member->is_executor = isset($executors[$oper_id]) ? 1 : 0;
            $member->is_controller = isset($controllers[$oper_id]) ? 1 : 0;
            $member->invited_id = $invited_id;
            $member->date_add = new Expression("NOW()");
            if (!$member->save()) {
                throw new Exception("Ошибка добавления участника: " . implode(", ", $member->getFirstErrors()));
            }
            $members[$oper_id] = $member;
        }
        $this->populateRelation('members', $members);

        if ($this->task) {
            $opers_see = ArrayHelper::index($this->task->oper_see, 'oper_id');
            if (!isset($opers_see[$oper_id])) {
                $oper_see = new Req3TaskOperStepOther();
                $oper_see->task_id = $this->task->id;
                $oper_see->oper_id = $oper_id;
                if ($oper_see->save()) {
                    $opers_see[$oper_id] = $oper_see;
                    $this->task->populateRelation('oper_see', $opers_see);
                }
            }
        }
    }

    public function isAccessView($identification)
    {
        return $this->get(function () use ($identification) {
            $oper = Opers::getOperByData($identification);
            if ($oper) {
                if ($this->creator_id == $oper->oper_id) return true;

                $member_ids = ArrayHelper::getColumn($this->members, 'oper_id');
                if (in_array($oper->oper_id, $member_ids)) return true;

                return $this->task ? $this->task->isAccessView($oper) : false;
            }
            return false;
        });
    }

    public function isActiveMember($identification)
    {
        return $this->get(function () use ($identification) {
            $oper = Opers::getOperByData($identification);
            if ($oper) {
                foreach ($this->members as $k => $member) {
                    if (empty($member->date_left) && $member->oper_id == $oper->oper_id) {
                        return true;
                    }
                }
            }
            return false;
        });
    }

    public function isAccessAddMessage($identification)
    {
        return $this->get(function () use ($identification) {
            if ($this->is_active == 1) {
                return $this->isActiveMember($identification);
            }
            return false;
        });
    }

    public function isAccessConnectSelf($identification)
    {
        return $this->get(function () use ($identification) {
            if ($this->is_active == 1) {
                if ($this->task) {
                    $oper = Opers::getOperByData($identification);
                    if ($oper) {

                        //контролеры
                        $oper_ids = ArrayHelper::getColumn($this->task->controllers, 'oper_id');
                        if (in_array($oper->oper_id, $oper_ids)) return true;

                        //исполнители
                        $oper_ids = ArrayHelper::getColumn($this->task->executors, 'oper_id');
                        if (in_array($oper->oper_id, $oper_ids)) return true;

                        //действующие
                        $oper_ids = ArrayHelper::getColumn($this->task->workers, 'oper_id');
                        if (in_array($oper->oper_id, $oper_ids)) return true;

                        //если он уже был в чате он может вернуться
                        $members = $this->members;
                        // убираем тех кто в чате
                        foreach ($members as $k => $member) {
                            if (empty($member->date_left)) {
                                unset($members[$k]);
                            }
                        }
                        $member_ids = ArrayHelper::getColumn($members, 'oper_id');
                        return in_array($oper->oper_id, $member_ids);
                    }
                }
            }
            return false;
        });
    }

    public function isAccessClose($identification)
    {
        return $this->get(function () use ($identification) {
            if ($this->is_active == 1) {
                $oper = Opers::getOperByData($identification);
                if ($oper) {
                    if ($this->creator_id == $oper->oper_id) return true;

                    foreach ($this->members as $member) {
                        if (empty($member->date_left) && ($member->is_executor == 1 || $member->is_controller == 1) && $member->oper_id == $oper->oper_id) {
                            return true;
                        }
                    }
                }

                $isAccessPerm = Yii::$app->authManager->checkAccess($oper->oper_id, "business.chat.close");
                if ($isAccessPerm) return true;
            }
            return false;
        });
    }

    public function isAccessLeave($identification)
    {
        return $this->get(function () use ($identification) {
            if ($this->is_active == 1) {
                $oper = Opers::getOperByData($identification);
                if ($oper) {
                    if ($this->creator_id == $oper->oper_id) return false;//создатель не может выйти

                    foreach ($this->members as $member) {
                        if (empty($member->date_left) && $member->oper_id == $oper->oper_id) {
                            return true;
                        }
                    }
                }
            }
            return false;
        });
    }

    public function isAccessInvite($identification)
    {
        return $this->get(function () use ($identification) {
            if ($this->is_active == 1) {
                $oper = Opers::getOperByData($identification);
                if ($oper) {
                    if ($this->creator_id == $oper->oper_id) return true;
                    foreach ($this->members as $member) {
                        if (empty($member->date_left) && $member->oper_id == $oper->oper_id) {
                            return true;
                        }
                    }
                }
            }
            return false;
        });
    }

    public function isAccessAddComplaint($identification)
    {
        return $this->get(function () use ($identification) {
            if ($this->is_active == 1) {
                return $this->isActiveMember($identification);
            }
            return false;
        });
    }

    public function sendTelegram($message_id = null)
    {
        if (empty($this->task->template->bot_important_id ?? null)) return false;
        $telegramService = Yii::$container->get(ProcessTelegramService::class);
        $bot = $telegramService->getById($this->task->template->bot_important_id);
        if (!$bot) return false;

        if ($message_id !== null && $this->last_message_id != $message_id) {
            return false;
        }


        $telegram = new HelperTelegram($bot->token, $bot->name);
        $max_len = HelperTelegram::LENGTH_MESSAGE;
        $before_sends = ArrayHelper::map(Req3TasksChatTelegramSend::find()->andWhere(['link_id' => $this->id])->all(), 'id', fn($it) => $it, 'chat_id');

        if ($this->is_active) {
            $text = '📟' . " <a href='" . Url::toRoute(['/process/chat/view', 'id' => $this->id], true) . "'>Чат на тему: {$this->topic}</a>\n";
            $temp_msg = '🙀' . " ... не поместились все сообщения ...\n";

            $msgs = "";
            foreach (array_reverse($this->messages) as $message) {
                if ($message->type == Req3TasksChatMessages::TYPE_OPER) {
                    $tmp = "\n<b>" . Opers::getFioOrFioDeletedHtml($message, 'oper', 'oper_id', false) . "</b>: " . $message->message;
                    foreach ($message->files as $file) {
                        $tmp .= " <a href='" . Url::toRoute(['/process/chat/get-file', 'file_id' => $file->id], true) . "'>[{$file->orig_name}]</a>";
                    }
                } else {
                    $tmp = "\n<code>" . $message->message . "</code>";
                }


                if (Str::len($text . $tmp . $msgs . $temp_msg) > $max_len) {
                    $msgs = $temp_msg . $msgs;
                    break;
                } else {
                    $msgs = $tmp . $msgs;
                }
            }
            $text .= $msgs;


            $sends = [];
            $fnct_check_alert_message = function ($last_message_id, $oper_id) {
                $ok = false;
                foreach ($this->messages as $message) {
                    if ($ok) {
                        //если после последнего полученного сообщения было не его и не информ сообщение значит надо алерт
                        if ($message->oper_id != $oper_id && $message->type == Req3TasksChatMessages::TYPE_OPER) {
                            return true;
                        }
                    }
                    if ($message->id == $last_message_id) {
                        $ok = true;
                    }
                }
                return false;
            };


            foreach ($this->members as $member) {
                if (empty($member->date_left)) {
                    if ($member->oper) {
                        foreach ($member->oper->telegrams_link as $telegram_link) {
                            if (!isset($sends[$telegram_link->telegram_id])) {
                                $sends[$telegram_link->telegram_id] = true;

                                /** @var Req3TasksChatTelegramSend $before_send */
                                $before_send = null;
                                if (isset($before_sends[$telegram_link->telegram_id]) && count($before_sends[$telegram_link->telegram_id]) > 0) {
                                    $key = array_key_first($before_sends[$telegram_link->telegram_id]);
                                    $before_send = $before_sends[$telegram_link->telegram_id][$key];
                                    unset($before_sends[$telegram_link->telegram_id][$key]);
                                }

                                if ($before_send) {
                                    if ($fnct_check_alert_message($before_send->last_message_id, $member->oper_id)) {
                                        $telegram->deleteMessage($before_send->chat_id, $before_send->message_id);
                                        $before_send->delete();
                                        $before_send = null;
                                    } else {
                                        $update = $telegram->editMessageText($before_send->chat_id, $before_send->message_id, null, $text, HelperTelegram::PARSE_MODE_HTML, true);
                                        if ($update->ok) {
                                            $before_send->last_message_id = $this->last_message_id;
                                            $before_send->date_send = new Expression("NOW()");
                                            $before_send->save();
                                        } else {
                                            $telegram->deleteMessage($before_send->chat_id, $before_send->message_id);
                                            $before_send->delete();
                                            $before_send = null;
                                        }
                                    }
                                }

                                if (!$before_send) {
                                    $send = $telegram->sendMessage($telegram_link->telegram_id, $text, HelperTelegram::PARSE_MODE_HTML, true);
                                    if ($send->ok) {
                                        $before_send = new Req3TasksChatTelegramSend();
                                        $before_send->link_id = $this->id;
                                        $before_send->last_message_id = $this->last_message_id;
                                        $before_send->chat_id = $telegram_link->telegram_id;
                                        $before_send->message_id = $send->result->message_id;
                                        $before_send->date_send = new Expression("NOW()");
                                        $before_send->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        foreach ($before_sends as $before_send) {
            foreach ($before_send as $send) {
                $telegram->deleteMessage($send->chat_id, $send->message_id);
                $send->delete();
            }
        }
        return true;
    }

    public function getActiveMembers()
    {
        return $this->get(function () {
            $members = $this->members;
            foreach ($members as $k => $member) {
                if (!empty($member->date_left)) {
                    unset($members[$k]);
                }
            }
            return $members;
        });
    }

    public function getMember($oper_id)
    {
        foreach ($this->members as $member) {
            if ($member->oper_id == $oper_id) {
                return $member;
            }
        }
        return null;
    }

    public function getCloseItemValue()
    {
        if ($this->close_item_id == self::CLOSE_ITEM_TIMEOUT) {
            return "От последнего сообщения прошло слишком много времен";
        }
        return $this->close_item->value ?? "-";
    }

    public function checkDateLastMessage()
    {
        if ($this->is_active == 1) {
            $last_message = strtotime($this->date_last_message);
            if (time() - $last_message > 6 * 60 * 60) {
                $this->is_active = 0;
                $this->date_close = new Expression("NOW()");
                $this->close_id = HelperOper::CREATOR_SYSTEM;
                $this->close_item_id = self::CLOSE_ITEM_TIMEOUT;
                $this->save();
            }
        }

    }

    // ============================================================================
    // ============================== STATIC ======================================
    // ============================================================================
}