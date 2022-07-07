<?php declare(strict_types=1);

namespace VKollin\Doctrine\BackedEnumFields\Bundle;

use Doctrine\DBAL\Types\Type;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use VKollin\Doctrine\BackedEnumFields\Type\NativeEnum;

/**
 * @codeCoverageIgnore
 */
final class DoctrineBackedEnumFieldsBundle extends Bundle
{
    public function boot(): void
    {
        $config = $this->container->getParameter('doctrine_backed_enum_fields.enum_types') ?? [];
        foreach ($config as $enumClass => $enumType) {
            $enumType ??= $enumClass;
            if (!Type::hasType($enumType)) {
                NativeEnum::registerEnumType($enumType, $enumClass);
            }
        }
    }
}
