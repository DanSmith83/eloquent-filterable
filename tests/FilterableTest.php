<?php


use DanSmith\Filterable\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FilterableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Dansmith\Filterable\Exceptions\FilterableException
     */
    public function testThrowsExceptionWhenFilterableAttributesNotSpecified()
    {
        $builder = \Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $foo = new Foo();
        $foo->scopeFilter($builder);
    }

    public function testReturnsOriginalBuilderWhenNoParametersProvided()
    {
        $builder = \Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $foo = new Bar();
        $foo->scopeFilter($builder);
    }

    public function testSearchesForEachParameterProvided()
    {
        $builder = \Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $builder->shouldReceive('where')->twice();

        $foo = new Bar();
        $foo->scopeFilter($builder, ['foo' => 'bar', 'bar' => 'foo']);
    }

    public function testSearchesOnCallable()
    {
        $builder = \Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $builder->shouldReceive('where')->once();

        $baz = new Baz;
        $baz->scopeFilter($builder, ['foo' => 'test']);
    }

    public function testSearchesMixed()
    {
        $builder = \Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $builder->shouldReceive('where')->twice();

        $baz = new Baz;
        $baz->scopeFilter($builder, ['foo' => 'test', 'bar' => 'test']);
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

class Baz extends Model{

    use Filterable;

    public function getFilterable()
    {
        return [
            'foo'   => function($query, $value) { $query->where('foo', '=', $value); return $query; },
            'bar'   => FooFilter::class
        ];
    }
}

class FooFilter implements \DanSmith\Filterable\Filter
{
    public function handle(Builder $query, $value)
    {
        return $query->where('foo', '=', $value);
    }
}