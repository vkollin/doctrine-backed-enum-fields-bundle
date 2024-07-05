<?php

declare(strict_types=1);

namespace VKollin\Doctrine\BackedEnumFields\IdGenerator;

use BackedEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\EntityMissingAssignedId;
use Doctrine\ORM\Id\AssignedGenerator;

/**
 * Special generator that also handles enums as identifiers.
 * Read here how to use it https://www.doctrine-project.org/projects/doctrine-orm/en/2.8/reference/annotations-reference.html#annref_customidgenerator
 * You need to use "GeneratedValue" attribute otherwise no custom generator will be used.
 */
class EnumIdGenerator extends AssignedGenerator
{
    /**
     * Can handle enums as identifiers.
     *
     * {@inheritdoc}
     *
     * @throws EntityMissingAssignedId
     */
    public function generateId(EntityManagerInterface $em, $entity): array
    {
        $identifier = parent::generateId($em, $entity);

        foreach ($identifier as $key => $value) {
            if ($value instanceof BackedEnum) {
                $identifier[$key] = $value->value;
            }
        }

        return $identifier;
    }
}
