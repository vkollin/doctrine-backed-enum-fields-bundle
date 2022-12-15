# Symfony Doctrine Enum Fields Bundle

This library provides support to [PHP Enums](https://wiki.php.net/rfc/enumerations), introduced in PHP 8.1,
within your Doctrine entities and will generate enum fields for you.

Doctrine introduced [kinda enum support](https://www.doctrine-project.org/2022/01/11/orm-2.11.html), but this will give
you just string fields.

## Thanks

This project was forked from [bpolaszek/doctrine-native-enums](https://github.com/bpolaszek/doctrine-native-enums) by [
Beno!t POLASZEK](https://github.com/bpolaszek) and heavily inspired
by [this blog post](https://knplabs.com/en/blog/how-to-map-a-php-enum-with-doctrine-in-a-symfony-project)

## Installation

```bash
composer require vkollin/doctrine-backed-enum-fields-bundle
```

## Usage

This library only works with [Backed enums](https://wiki.php.net/rfc/enumerations#backed_enums).

### In a Symfony project

#### 1. Declare the bundle.

```php
// config/bundles.php

return [
    // ...
    VKollin\Doctrine\BackedEnumFields\Bundle\DoctrineBackedEnumFieldsBundle::class => ['all' => true],
];
```

#### 2. Register enums in your configuration.

```yaml
# config/packages/doctrine_backed_enum_fields.yaml

doctrine_backed_enum_fields:
  enum_types:
    App\Entity\StatusEnum: ~
    #App\Entity\StatusEnum: status # Alternatively, if you want your type to be named "status"
```

#### 3. Use them in your entities.

```php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class Book
{
    #[
        ORM\Id,
        ORM\Column(unique: true),
        ORM\GeneratedValue(strategy: 'AUTO'),
    ]
    public int $id;

    #[ORM\Column]
    public string $name;

    #[ORM\Column(type: StatusEnum::class)]
    public StatusEnum $status;
}
```

### Enums in your key

You need to use a custom id generator to use enums as your key. It is also required to set the GeneratedValue strategy to CUSTOM.
Otherwise the custom id generator will not be used. Nothing will be generated here though.

```php
#[ORM\Column(name: 'type', type: StatusEnum::class, nullable: false)]
#[ORM\GeneratedValue(strategy: 'CUSTOM')]
#[ORM\CustomIdGenerator(EnumIdGenerator::class)]
#[ORM\Id]
private StatusEnum $status,
```

Then you also need to tag the custom id generator as a doctrine id_generator in your services.yaml

```yaml
    VKollin\Doctrine\BackedEnumFields\IdGenerator\EnumIdGenerator:
        class: VKollin\Doctrine\BackedEnumFields\IdGenerator\EnumIdGenerator
        tags: [ 'doctrine.id_generator' ]
```

### In other projects using Doctrine

```php
use App\Entity\StatusEnum;
use BenTools\Doctrine\NativeEnums\Type\NativeEnum;
use Doctrine\DBAL\Types\Type;

NativeEnum::registerEnumType(StatusEnum::class);
// NativeEnum::registerEnumType('status', StatusEnum::class); // Alternatively, if you want your type to be named "status"
```

## License

MIT.

_Ceterum censeo Doctrinam esse delendam_
