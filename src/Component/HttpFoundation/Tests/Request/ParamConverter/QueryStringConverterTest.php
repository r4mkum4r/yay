<?php

namespace Component\HttpFoundation\Tests\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Component\HttpFoundation\Request\ParamConverter\QueryStringConverter;

class QueryStringConverterTest extends TestCase
{
    public function createConfiguration(
        string $converterName = 'QueryString',
        string $parameterName = 'foo',
        array $options = []
    ): ParamConverter {
        $configuration = $this->getMockBuilder(ParamConverter::class)
                              ->disableOriginalConstructor()
                              ->setMethods(['getConverter', 'getName', 'getOptions'])
                              ->getMock();

        $configuration->method('getConverter')
                      ->willReturn($converterName);

        $configuration->method('getName')
                      ->willReturn($parameterName);

        $configuration->method('getOptions')
                      ->willReturn($options);

        return $configuration;
    }

    public function test_param_converter_is_supported(): void
    {
        $configuration = $this->createConfiguration();

        $this->assertTrue((new QueryStringConverter())->supports($configuration));
    }

    public function test_param_converter_is_not_supported(): void
    {
        $configuration = $this->createConfiguration('QueryString2');

        $this->assertFalse((new QueryStringConverter())->supports($configuration));
    }

    public function test_query_parameter_is_applied_as_attribute(): void
    {
        $configuration = $this->createConfiguration();
        $request = Request::create('/', 'GET', ['foo' => 'bar']);

        (new QueryStringConverter())->apply($request, $configuration);
        $this->assertEquals('bar', $request->attributes->get('foo'));
    }

    public function test_query_parameter_is_applied_as_attribute_with_field(): void
    {
        $configuration = $this->createConfiguration('HeaderField', 'foo', ['field' => 'baz']);
        $request = Request::create('/', 'GET', ['baz' => 'bar']);

        (new QueryStringConverter())->apply($request, $configuration);
        $this->assertEquals('bar', $request->attributes->get('foo'));
    }

    public function test_query_parameter_is_not_set(): void
    {
        $configuration = $this->createConfiguration();
        $request = Request::create('/', 'GET');

        (new QueryStringConverter())->apply($request, $configuration);
        $this->assertFalse($request->attributes->has('foo'));
    }
}
