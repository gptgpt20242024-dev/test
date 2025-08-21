<?php

namespace app\modules\process\models;

use app\components\Date;
use app\modules\process\models\task_archive\TaskArchive;
use yii\base\Model;

class FormReq3SearchArchive extends Model
{
    public $templateIds;
    public $name;
    public $dateRange;

    public function attributeLabels()
    {
        return [
            'dateRange' => 'Период создания задачи',
            'name' => 'Название задачи',
            'templateIds' => 'Шаблоны'
        ];
    }

    public function formName()
    {
        return '';
    }

    public function rules(): array
    {
        return [
            [['templateIds'], 'integer'],
            [['name', 'dateRange'], 'safe'],
            ['dateRange', 'validateDateRange'],
        ];
    }

    public function validateDateRange($attribute)
    {
        if (!empty($this->dateRange)) {
            $arr = explode(' - ', (string)$this->dateRange);
            if (count($arr) !== 2) {
                $this->addError($attribute, 'Неверный диапазон дат.');
                return;
            }
            [$from, $to] = $arr;
            if (strtotime($from) === false || strtotime($to) === false || strtotime($from) > strtotime($to)) {
                $this->addError($attribute, 'Начальная дата больше конечной.');
            }
        }
    }

    public function find()
    {
        $query = TaskArchive::find();

        if (!$this->validate()) {
            $query->where('0=1');
        } else {
            if ($this->templateIds) {
                $query->andWhere(['template_id' => $this->templateIds]);
            }
            if ($this->name) {
                $query->andFilterWhere(['like', 'task_name', $this->name]);
            }
            if ($this->dateRange) {
                [$from, $to] = explode(' - ', $this->dateRange);
                $from = (new Date($from))->format('Y-m-d 00:00:00');
                $to = (new Date($to))->addDays()->format('Y-m-d 00:00:00');
                $query->andWhere('task_date_create >= :from AND task_date_create < :to', [':from' => $from, ':to' => $to]);
            }
        }

        return $query;
    }
}
