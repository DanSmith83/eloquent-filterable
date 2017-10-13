#Eloquent Filterable

A simple package for filtering records, using varying user input.

##Installation

Add the package to your composer.json file and run composer update:

```json
{
	"require" : {
	  "dansmith/eloquent-filterable": "dev-master"
	}
}
```

##Basic Use

Import the trait and use in your Eloquent model

```php
use DanSmith\Filterable\Filterable;

class Page extends Model {

    use Filterable;

}
```

Specify the attributes you want to filter by (any values not specified here will be ignored):

```php
protected $filterable = ['category_id', 'created_by'];
```

Alternatively, if you want to provide more complex filters, you can override the getFilterable method.  This makes it possible to provide either a closure or a custom class.
```php
public function getFilterable()
{
    return [
    	'category_id',
    	'created_by',
    	'starts_with' => function($query, $value) { return $query->where('title', 'LIKE', $value.'%'); }
    ];
}
```

Run an Eloquent query with using your parameters

```php
$parameters = ['category_id' => 1, 'created_by' => 2];
$pages = Page::filter($parameters)->orderBy('title', 'asc')->paginate();
```

Taking parameters directly from the URL
```php
$pages = Page::filter($request->all())->orderBy('title', 'asc')->paginate();
```
