<?php
namespace app\modules\process2\data\widget\type\view;

use app\modules\process2\data\dto\type\DataItemServiceDto;
use app\modules\process2\data\widget\BaseIdentifierViewWidget;

final class ServiceViewWidget extends BaseIdentifierViewWidget
{
    public DataItemServiceDto $item;

    public function run(): string
    {
        $name = $this->item->service->name ?? ('#' . ($this->item->valueId ?? ''));
        return 'Service: ' . $name;
    }
}
