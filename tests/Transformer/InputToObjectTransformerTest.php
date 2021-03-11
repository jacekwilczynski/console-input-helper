<?php

declare(strict_types=1);

namespace ConVal\Transformer;

use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class InputToObjectTransformerTest extends TestCase
{
    private const DATASET_1 = [
        'firstName' => 'Anna',
        'last_name' => 'Lee',
        '--heightInCm' => '124',
        '--likes-cottage-cheese' => null,
    ];

    private const DATASET_2 = [
        'firstName' => 'George',
        'last_name' => 'Smith',
        '-h' => '155',
        '--favourite-foods' => ['a', 'b', 'c'],
    ];

    private InputToObjectTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new InputToObjectTransformer(
            new InputToArrayTransformer(new DashesAndUnderscoresToCamelCase()),
            new Serializer([new ObjectNormalizer()]),
        );
    }

    public function testThrowsAnExceptionIfTheTargetClassDoesNotExist(): void
    {
        $this->expectException(Exception::class);
        $this->transformer->transform($this->getInput(), 'Some\\NonExistentClass');
    }

    /**
     * @dataProvider targetClassProvider
     */
    public function testReturnsAnInstanceOfTheTargetClass(string $targetClass): void
    {
        $result = $this->transformer->transform($this->getInput(), $targetClass);
        self::assertInstanceOf($targetClass, $result);
    }

    public function testPutsValidValuesInDtoWithJustPublicProperties(): void
    {
        /** @var DtoWithJustPublicProperties $result */
        $result = $this->transformer->transform(
            $this->getInput(self::DATASET_1),
            DtoWithJustPublicProperties::class,
        );

        self::assertSame('Anna', $result->firstName);
        self::assertSame('Lee', $result->lastName);
        self::assertSame(124, $result->heightInCm);
        self::assertTrue($result->likesCottageCheese);
        self::assertSame([], $result->favouriteFoods);
        self::assertNull($result->ignoredParameter);
    }

    public function testCanTransformToDtoWithConstructorAndGetters(): void
    {
        /** @var DtoWithConstructorAndGetters $result */
        $result = $this->transformer->transform(
            $this->getInput(self::DATASET_2),
            DtoWithConstructorAndGetters::class,
        );

        self::assertSame('George', $result->getFirstName());
        self::assertSame('Smith', $result->getLastName());
        self::assertSame(155, $result->getHeightInCm());
        self::assertFalse($result->likesCottageCheese());
        self::assertSame(['a', 'b', 'c'], $result->getFavouriteFoods());
        self::assertNull($result->getIgnoredParameter());
    }

    public function targetClassProvider(): iterable
    {
        return [
            [stdClass::class],
            [DtoWithJustPublicProperties::class],
            [DtoWithConstructorAndGetters::class],
        ];
    }

    private function getInput(array $data = self::DATASET_1): ArrayInput
    {
        return new ArrayInput($data, new InputDefinition([
            new InputArgument('firstName'),
            new InputArgument('last_name'),
            new InputOption('heightInCm', 'h', InputOption::VALUE_REQUIRED),
            new InputOption('likes-cottage-cheese', null, InputOption::VALUE_NONE),
            new InputOption(
                'favourite-foods',
                null,
                InputOption::VALUE_REQUIRED + InputOption::VALUE_IS_ARRAY,
            ),
        ]));
    }
}

class DtoWithJustPublicProperties
{
    public string $firstName;
    public string $lastName;
    public int $heightInCm;
    public bool $likesCottageCheese;
    public array $favouriteFoods;
    public ?string $ignoredParameter = null;
}

class DtoWithConstructorAndGetters
{
    private string $firstName;
    private string $lastName;
    private int $heightInCm;
    private bool $likesCottageCheese;
    private array $favouriteFoods;
    private ?string $ignoredParameter = null;

    public function __construct(string $firstName, string $lastName, int $heightInCm, bool $likesCottageCheese, array $favouriteFoods)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->heightInCm = $heightInCm;
        $this->likesCottageCheese = $likesCottageCheese;
        $this->favouriteFoods = $favouriteFoods;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getHeightInCm(): int
    {
        return $this->heightInCm;
    }

    public function likesCottageCheese(): bool
    {
        return $this->likesCottageCheese;
    }

    public function getFavouriteFoods(): array
    {
        return $this->favouriteFoods;
    }

    public function getIgnoredParameter(): ?string
    {
        return $this->ignoredParameter;
    }
}