<?php

namespace app\modules\process2\dto\data;

class DataItemDto
{
    public function __construct(
        public int $id,
        public int $type,
        public int $identifierId,
        public ?int $valueId = null,
        public ?string $valueText = null,
    ) {
    }
}
