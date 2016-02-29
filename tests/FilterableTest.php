<?php


use DanSmith\Filterable\Filterable;
use Illuminate\Database\Eloquent\Model;

class FilterableTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->builder = \Mockery::mock('Illuminate\Database\Eloquent\Builder');
    }

    /**
     * @expectedException \Dansmith\Filterable\Exceptions\FilterableException
     */
    public function testThrowsExceptionWhenFilterableAttributesNotSpecified()
    {
        $foo = new Foo();
        $foo->scopeFilter($this->builder);
    }

    public function testReturnsOriginalBuilderWhenNoParametersProvided()
    {
        $foo = new Bar();
        $foo->scopeFilter($this->builder);
    }

    public function testSearchesForEachParameterProvided()
    {
        $this->builder->shouldReceive('where')->twice();
        
        $foo = new Bar();
        $foo->scopeFilter($this->builder, ['foo' => 'bar', 'bar' => 'foo']);
    }
}

class Foo extends Model
{
    use Filterable;
}

class Bar extends Model
{
    use Filterable;

    protected $filterable = [
        'foo',
        'bar'
    ];
}