<?php

namespace app\modules\process2\components\data\dto;

class DataItemDto
{
    public int     $id;
    public int     $type;
    public int     $identifierId;
    public ?int    $valueId   = null;
    public ?string $valueText = null;

    public function __construct(
        int $id,
        int $type,
        int $identifierId,
        ?int $valueId = null,
        ?string $valueText = null,
    ) {
        $this->valueText = $valueText;
        $this->valueId = $valueId;
        $this->identifierId = $identifierId;
        $this->type = $type;
        $this->id = $id;
    }
}
