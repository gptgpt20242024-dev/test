<?php

namespace app\modules\process\factories;

use app\modules\process\models\task_data\Req3TasksDataFiles;
use app\modules\process\models\task_data\Req3TasksDataItemAddress;
use app\modules\process\models\task_data\Req3TasksDataItemBaskets;
use app\modules\process\models\task_data\Req3TasksDataItemBasketServices;
use app\modules\process\models\task_data\Req3TasksDataItemCommunicationChannels;
use app\modules\process\models\task_data\Req3TasksDataItemDocs;
use app\modules\process\models\task_data\Req3TasksDataItemIdentifierComments;
use app\modules\process\models\task_data\Req3TasksDataItemProjectTree;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\models\task_data\Req3TasksDataItemsOperRoleComment;
use app\modules\process\models\task_data\Req3TasksDataItemSteps;
use app\modules\process\models\task_data\Req3TasksDataItemWhBalance;
use yii\db\ActiveRecord;

class ArchiveDataDtoFactory
{
    public const DATA_CLASS_MAP = [
        'file'                       => Req3TasksDataFiles::class,
        'address_link'               => Req3TasksDataItemAddress::class,
        'doc_link'                   => Req3TasksDataItemDocs::class,
        'communication_channel_link' => Req3TasksDataItemCommunicationChannels::class,
        'template_steps'             => Req3TasksDataItemSteps::class,
        'basket'                     => Req3TasksDataItemBaskets::class,
        'oper_role_comment'          => Req3TasksDataItemsOperRoleComment::class,
        'balanceItems'               => Req3TasksDataItemWhBalance::class,
        'node'                       => Req3TasksDataItemProjectTree::class,
        'check_identifier_comments'  => Req3TasksDataItemIdentifierComments::class,
        'children'                   => Req3TasksDataItems::class,
    ];

    public static function serializeDataItem(Req3TasksDataItems $item): array
    {
        $data = [
            'identifier_id' => $item->identifier_id,
            'type'          => $item->type,
            'value_id'      => $item->value_id,
            'value_text'    => $item->value_text,
            'value_number'  => $item->value_number,
            'oper_id'       => $item->oper_id,
        ];

        foreach (array_keys(self::DATA_CLASS_MAP) as $relation) {
            $related = $item->{$relation};
            if (empty($related)) {
                continue;
            }
            if ($related instanceof ActiveRecord) {
                $data[$relation] = self::serializeActiveRecord($related);
            } elseif (is_array($related)) {
                $data[$relation] = [];
                foreach ($related as $rel) {
                    if ($rel instanceof ActiveRecord) {
                        $data[$relation][] = self::serializeActiveRecord($rel);
                    }
                }
            }
        }

        return $data;
    }

    private static function serializeActiveRecord(ActiveRecord $model): array
    {
        if ($model instanceof Req3TasksDataItems) {
            return self::serializeDataItem($model);
        }

        if ($model instanceof Req3TasksDataItemBaskets) {
            return self::serializeBasket($model);
        }

        return self::serializeDefault($model);
    }

    private static function serializeDefault(ActiveRecord $model): array
    {
        return [
            'attributes' => $model->getAttributes(null, ['id']),
        ];
    }

    private static function serializeBasket(Req3TasksDataItemBaskets $model): array
    {
        $result = self::serializeDefault($model);

        if (!empty($model->services)) {
            $result['services'] = [];
            foreach ($model->services as $service) {
                $result['services'][] = self::serializeDefault($service);
            }
        }

        return $result;
    }

    public static function createDataItem(array $row): Req3TasksDataItems
    {
        $item = new Req3TasksDataItems([
            'identifier_id' => $row['identifier_id'] ?? null,
            'type'          => $row['type'] ?? null,
            'value_id'      => $row['value_id'] ?? null,
            'value_text'    => $row['value_text'] ?? null,
            'value_number'  => $row['value_number'] ?? null,
            'oper_id'       => $row['oper_id'] ?? null,
        ]);

        foreach (array_keys(self::DATA_CLASS_MAP) as $relation) {
            if (isset($row[$relation])) {
                $value = $row[$relation];
                if (is_array($value)) {
                    if (self::isAssoc($value)) {
                        $related = self::createActiveRecord($relation, $value);
                        if ($related) {
                            $item->populateRelation($relation, $related);
                        }
                    } else {
                        $list = [];
                        foreach ($value as $val) {
                            $rel = self::createActiveRecord($relation, $val);
                            if ($rel) {
                                $list[] = $rel;
                            }
                        }
                        $item->populateRelation($relation, $list);
                    }
                }
            }
        }

        return $item;
    }

    private static function createActiveRecord(string $relation, array $config)
    {
        $class = self::DATA_CLASS_MAP[$relation] ?? null;
        if ($class === null) {
            return null;
        }

        if ($class === Req3TasksDataItems::class) {
            return self::createDataItem($config);
        }

        if ($class === Req3TasksDataItemBaskets::class) {
            return self::unserializeBasket($config);
        }

        return self::unserializeDefault($class, $config);
    }

    private static function unserializeDefault(string $class, array $config): ActiveRecord
    {
        return new $class($config['attributes'] ?? []);
    }

    private static function unserializeBasket(array $config): Req3TasksDataItemBaskets
    {
        $model = new Req3TasksDataItemBaskets($config['attributes'] ?? []);

        if (!empty($config['services'])) {
            $services = [];
            foreach ($config['services'] as $row) {
                $services[] = self::unserializeDefault(Req3TasksDataItemBasketServices::class, $row);
            }
            $model->populateRelation('services', $services);
        }

        return $model;
    }

    private static function isAssoc(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
