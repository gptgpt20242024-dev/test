<?php

namespace app\modules\process\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\process\models\task_archive\TaskArchive;

class FormReq3SearchArchive extends Model
{
    public $template_id;
    public $template_name;
    public $dateRange;

    public function rules(): array
    {
        return [
            [['template_id'], 'integer'],
            [['template_name', 'dateRange'], 'safe'],
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

    public function search(array $params): ActiveDataProvider
    {
        $query = TaskArchive::find();

        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');
        } else {
            if ($this->template_id) {
                $query->andWhere(['template_id' => $this->template_id]);
            }
            if ($this->template_name) {
                $query->andFilterWhere(['like', 'template_name', $this->template_name]);
            }
            if ($this->dateRange) {
                [$from, $to] = explode(' - ', $this->dateRange);
                $query->andWhere(['between', 'task_date_create', $from, $to]);
            }
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['task_id' => SORT_DESC],
            ],
        ]);
    }
}
