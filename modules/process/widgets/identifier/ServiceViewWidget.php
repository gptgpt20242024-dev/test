<?php
namespace app\modules\process\widgets\identifier;

use app\modules\process\dto\data_item\DataItemServiceDto;

final class ServiceViewWidget extends BaseIdentifierViewWidget
{
    public DataItemServiceDto $item;

    public function run(): string
    {
        $name = $this->item->service->name ?? ('#' . ($this->item->valueId ?? ''));
        return 'Service: ' . $name;
    }
}
