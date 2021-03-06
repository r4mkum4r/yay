<?php

namespace Component\HttpFoundation\Tests\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Component\HttpFoundation\Request\ParamConverter\HeaderFieldConverter;

class HeaderFieldConverterTest extends TestCase
{
    public function createConfiguration(
        string $converterName = 'HeaderField',
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

        $this->assertTrue((new HeaderFieldConverter())->supports($configuration));
    }

    public function test_param_converter_is_not_supported(): void
    {
        $configuration = $this->createConfiguration('HeaderField2');

        $this->assertFalse((new HeaderFieldConverter())->supports($configuration));
    }

    public function test_header_field_is_applied_as_attribute(): void
    {
        $configuration = $this->createConfiguration();
        $request = Request::create('/', 'GET');
        $request->headers->set('foo', 'bar');

        (new HeaderFieldConverter())->apply($request, $configuration);
        $this->assertEquals('bar', $request->attributes->get('foo'));
    }

    public function test_header_field_is_applied_as_attribute_with_field(): void
    {
        $configuration = $this->createConfiguration('HeaderField', 'foo', ['field' => 'baz']);
        $request = Request::create('/', 'GET');
        $request->headers->set('baz', 'bar');

        (new HeaderFieldConverter())->apply($request, $configuration);
        $this->assertEquals('bar', $request->attributes->get('foo'));
    }

    public function test_header_field_is_not_set(): void
    {
        $configuration = $this->createConfiguration();
        $request = Request::create('/', 'GET');

        (new HeaderFieldConverter())->apply($request, $configuration);
        $this->assertFalse($request->attributes->has('foo'));
    }
}
