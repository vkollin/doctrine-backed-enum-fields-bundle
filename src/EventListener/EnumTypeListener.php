<?php declare(strict_types=1);

namespace VKollin\Doctrine\BackedEnumFields\EventListener;


use Doctrine\DBAL\Schema\Column;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use VKollin\Doctrine\BackedEnumFields\Type\NativeEnum;

class EnumTypeListener
{
    public function postGenerateSchema(GenerateSchemaEventArgs $eventArgs)
    {
        $columns = [];

        foreach ($eventArgs->getSchema()->getTables() as $table) {
            foreach ($table->getColumns() as $column) {
                if ($column->getType() instanceof NativeEnum) {
                    $columns[] = $column;
                }
            }
        }

        /** @var Column $column */
        foreach ($columns as $column) {
            /** @var NativeEnum $type */
            $type = $column->getType();
            $enum = $type->getName();

            $cases = array_map(
                fn($enumItem) => "'{$enumItem->value}'",
                $enum::cases()
            );

            $hash = md5(implode(',', $cases));

            $column->setComment(
                trim(
                    sprintf(
                        '%s (DC2Enum:%s)',
                        $column->getComment(),
                        $hash
                    )
                )
            );
        }
    }
}
