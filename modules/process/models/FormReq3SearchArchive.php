<?php

namespace app\modules\process\models;

use app\modules\process\models\task_archive\TaskArchive;
use yii\base\Model;

class FormReq3SearchArchive extends Model
{
    public $templateId;
    public $templateName;
    public $dateRange;

    public function rules(): array
    {
        return [
            [['templateId'], 'integer'],
            [['templateName', 'dateRange'], 'safe'],
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
            if ($this->templateId) {
                $query->andWhere(['template_id' => $this->templateId]);
            }
            if ($this->templateName) {
                $query->andFilterWhere(['like', 'template_name', $this->templateName]);
            }
            if ($this->dateRange) {
                [$from, $to] = explode(' - ', $this->dateRange);
                $query->andWhere(['between', 'task_date_create', $from, $to]);
            }
        }

        return $query;
    }
}
