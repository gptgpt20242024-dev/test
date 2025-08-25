<?php

namespace app\modules\process2\factories\data;

use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process2\dto\data\DataItemDto;
use app\modules\process2\services\data\DataItemIdentifierRegistry;

final class DataItemDtoFactory
{
    public function __construct(private DataItemIdentifierRegistry $registry)
    {
    }

    public function create(Req3TasksDataItems $model): DataItemDto
    {
        $class = $this->registry->getDtoClass((int)$model->type);
        return new $class(
            id: (int)$model->id,
            type: (int)$model->type,
            identifierId: (int)$model->identifier_id,
            valueId: $model->value_id ? (int)$model->value_id : null,
            valueText: $model->value_text,
        );
    }
}
