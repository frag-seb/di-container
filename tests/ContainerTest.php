<?php

declare(strict_types=1);

namespace Tests\FragSeb\Container;

use FragSeb\Container\Container;
use FragSeb\Container\ContainerInterface;
use FragSeb\Container\Exception\DependencyException;
use FragSeb\Container\Exception\Exception;
use FragSeb\Container\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use ReflectionClass;
use stdClass;
use Tests\FragSeb\Container\Assets\Bar;
use Tests\FragSeb\Container\Assets\Baz;
use Tests\FragSeb\Container\Assets\Foo;
use Tests\FragSeb\Container\Assets\FooBar;

/**
 * @covers \FragSeb\Container\Container
 * @covers \FragSeb\Container\ServiceBag
 * @covers \FragSeb\Container\ParameterBag
 */
final class ContainerTest extends TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @before
     */
    public function init(): void
    {
        $this->container = new Container();
    }

    public function testInstance(): void
    {
        self::assertInstanceOf(BaseContainerInterface::class, $this->container);
        self::assertInstanceOf(ContainerInterface::class, $this->container);
    }

    /**
     * @dataProvider validData
     */
    public function testContainer(string $id, $expected, $service = null, array $parameters = []): void
    {
        $this->container->set($id, $service, $parameters);

        /** @var Foo $object1 */
        $object1 = $this->container->get($id);
        $object2 = $this->container->get($id);

        self::assertSame($object1, $object2);
        self::assertSame($expected, $object1->getName());
    }

    public function validData(): array
    {
        return [
            [
                'id' => Foo::class,
                'expected' => 'test',
                'service' => null,
                'parameters' => [
                    'name' => 'test',
                ],
            ],
            [
                'id' => Foo::class,
                'expected' => 'test1',
                'service' => null,
                'parameters' => [
                    'name' => 'test1',
                    'name1' => 'test2',
                    'name2' => 'test3',
                ],
            ],
            [
                'id' => Foo::class,
                'expected' => 'test1',
                'service' => null,
                'parameters' => [
                    'name1' => 'test2',
                    'name2' => 'test3',
                    'name' => 'test1',
                ],
            ],
            [
                'id' => Foo::class,
                'expected' => 'test',
                'service' => Foo::class,
                'parameters' => [
                    'name' => 'test',
                ],
            ],
            [
                'id' => Foo::class,
                'expected' => null,
                'service' => Foo::class,

            ],
            [
                'id' => 'app.service.test',
                'expected' => 'test',
                'service' => Foo::class,
                'parameters' => [
                    'name' => 'test',
                ],
            ],
            [
                'id' => 'app.service.test2',
                'expected' => 'test',
                'service' => static function (ContainerInterface $container, array $parameters): stdClass {
                    $class = new class ($parameters) extends stdClass {

                        /**
                         * @var mixed
                         */
                        private $name;

                        public function __construct(array $parameters = [])
                        {
                            $this->name = $parameters[0]->getValue();
                        }

                        public function getName()
                        {
                            return $this->name;
                        }

                    };

                    return $class;
                },
                'parameters' => [
                    'name' => 'test',
                ],
            ],
        ];
    }

    public function testContainerWithSeveralServices(): void
    {
        $this->container->set('app.foo.test', Foo::class);
        $this->container->set(Foo::class);
        $this->container->set(Bar::class);

        $object1 = $this->container->get('app.foo.test');
        $object2 = $this->container->get(Foo::class);
        $object3 = $this->container->get(Bar::class);

        self::assertInstanceOf(Foo::class, $object1);
        self::assertInstanceOf(Foo::class, $object2);
        self::assertInstanceOf(Bar::class, $object3);
        self::assertNotSame($object1, $object2);
        self::assertNotSame($object1, $object3);
        self::assertNotSame($object2, $object3);
    }

    public function testHas(): void
    {
        self::assertFalse($this->container->has(Foo::class));
        $this->container->set(Foo::class);
        self::assertTrue($this->container->has(Foo::class));
    }

    public function testTheClassCanAlsoBeInitialized(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The class `Tests\FragSeb\Container\Assets\Baz` cannot be initialized');

        $this->container->set(Baz::class);

        $this->container->get(Baz::class);
    }

    public function testIfTheServiceIsNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('The service with the id `Tests\FragSeb\Container\Assets\Foo` cannot be found');

        $this->container->get(Foo::class);
    }

    public function testIfTheDependencyCannotBeResolved(): void
    {
        $this->expectException(DependencyException::class);
        $this->expectExceptionMessage('The class dependency for the argument `name` cannot be resolved');

        $reflector = new ReflectionClass(FooBar::class);
        $constructor = $reflector->getConstructor();
        $this->container->getDependencies($constructor->getParameters(), []);
    }
}
