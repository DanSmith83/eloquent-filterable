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

Run an Eloquent query with using your parameters

```php
$pages = Page::filter($request->all())->orderBy('title', 'asc')->paginate();
```
