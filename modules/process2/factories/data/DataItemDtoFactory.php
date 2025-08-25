<?php

namespace app\modules\process2\factories\data;

use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process2\dto\data\DataItemDto;
use app\modules\process2\services\data\DataItemIdentifierRegistry;

final class DataItemDtoFactory
{
    private DataItemIdentifierRegistry $registry;

    public function __construct(DataItemIdentifierRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function createAll(array $models): array
    {
        $dtos = [];
        foreach ($models as $model) {
            $dtos[] = $this->create($model);
        }
        return $dtos;
    }

    public function create(Req3TasksDataItems $model): DataItemDto
    {
        /** @var class-string<DataItemDto> $class */
        $class = $this->registry->getDtoClass($model->type);
        return new $class(
             $model->id,
            $model->type,
            $model->identifier_id,
             $model->value_id ? $model->value_id : null,
             $model->value_text,
        );
    }
}
