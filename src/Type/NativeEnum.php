<?php declare(strict_types=1);

namespace VKollin\Doctrine\BackedEnumFields\Type;

use BackedEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use LogicException;
use ReflectionEnum;

final class NativeEnum extends Type
{
    private string $name;
    /** @var class-string<BackedEnum> */
    private string $class;
    private BackedEnumType $type;

    public static function registerEnumType(string $enumType, ?string $enumClass = null): void
    {
        $enumClass ??= $enumType;
        if (!is_a($enumClass, BackedEnum::class, true)) {
            throw new InvalidArgumentException(sprintf('Class `%s` is not a valid enum.', $enumClass));
        }

        self::addType($enumType, self::class);

        $type        = self::getType($enumType);
        $type->name  = $enumType;
        $type->class = $enumClass;
        $type->type  = self::detectEnumType($enumClass);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $values = array_map(
            function ($val) {
                return $this->type === BackedEnumType::INT ? (string) $val->value : "'" . $val->value . "'";
            },
            $this->class::cases()
        );

        return 'ENUM(' . implode(', ', $values) . ')';
    }

    /** @return class-string<BackedEnum> */
    public function getName(): string
    {
        return $this->name ?? throw new LogicException(
            sprintf(
                'Class `%s` cannot be used as primary type; register your own types with %s::registerEnumType() instead.',
                __CLASS__,
                __CLASS__,
            )
        );
    }

    /**
     * @param BackedEnum|null $enum
     */
    public function convertToDatabaseValue(mixed $enum, AbstractPlatform $platform): int|string|null
    {
        if (null === $enum) {
            return null;
        }

        if (!$enum instanceof BackedEnum) {
            $class = $this->class;
            if (false === enum_exists($class, true)) {
                throw new LogicException("This class should be an enum: $class");
            }
            return $class::from($enum)->value;
        }

        return $enum->value;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?BackedEnum
    {
        if (null === $value) {
            return null;
        }

        $value = $this->type->cast($value);

        /** @var BackedEnum $class */
        $class = $this->class;

        return $class::from($value);
    }

    public static function detectEnumType(string $enumClass): BackedEnumType
    {
        $type = (new ReflectionEnum($enumClass))->getBackingType()?->getName();

        return 'int' === $type ? BackedEnumType::INT : BackedEnumType::STRING;
    }

    /**
     * @codeCoverageIgnore
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
