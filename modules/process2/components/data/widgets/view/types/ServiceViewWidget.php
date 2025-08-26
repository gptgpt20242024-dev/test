<?php
namespace app\modules\process2\components\data\widgets\view\types;

use app\modules\process2\components\data\dto\types\DataItemServiceDto;
use app\modules\process2\components\data\widgets\view\BaseIdentifierViewWidget;

final class ServiceViewWidget extends BaseIdentifierViewWidget
{
    public DataItemServiceDto $item;

    public function run(): string
    {
        $name = $this->item->service->name ?? ('#' . ($this->item->valueId ?? ''));
        return 'Service: ' . $name;
    }
}
