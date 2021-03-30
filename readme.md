# Helpful Laravel model traits/interfaces

I've helped myself with these code snippets. Hope someone else would be helped too.

## Quick start: examples

### Activation

```php
class Membership extends \Illuminate\Datablase\Eloquent\Model, \Yuptogun\LaravelModel\Interfaces\ActivatableModel
{
    use \Yuptogun\LaravelModel\Traits\HasActivationAttributes;

    public function getStartsAtFieldAttribute()
    {
        return 'membership_begins_at';
    }
    public function getEndsAtFieldAttribute()
    {
        return 'membership_expires_at';
    }
}

if (!$user->membership->is_active) {
    throw new \Exception('please get a membership!');
}
```

### Rotating values

```php
class User extends \Illuminate\Datablase\Eloquent\Model, \Yuptogun\LaravelModel\Interfaces\ValueRotatingModel
{
    use \Yuptogun\LaravelModel\Traits\HasRotatingAttributes;

    public function getRotatingFieldsAttribute()
    {
        return [
            'hidden' => [1, 0],
        ];
    }
}

if ($user->switchField('hidden')) {
    return 'switched visibility!';
}
```

### Sortable

```php
class Episode extends \Illuminate\Datablase\Eloquent\Model, \Yuptogun\LaravelModel\Interfaces\SortableModel
{
    use \Yuptogun\LaravelModel\Traits\HasSortableOrderAttribute;

    public function getSortableOrderFieldAttribute()
    {
        return 'episode_sort';
    }
}

$episode_sort = request()->input('episode');
if ((new Episode)->sortBySortableOrder($episode_sort)) {
    return 'reordered!';
}
```

### Recursive models

```php
class Curriculum extends \Illuminate\Datablase\Eloquent\Model, \Yuptogun\LaravelModel\Interfaces\RecursiveModel
{
    use \Yuptogun\LaravelModel\Traits\IsRecursiveModel;

    public function getCteKeyChildAttribute()
    {
        return 'crcl_no';
    }
    public function getCteKeyParentAttribute()
    {
        return 'p_crcl_no';
    }
}

if ($curri->allParents()->grandParents()->first()->crcl_no == 2) {
    return 'Second grade curriculum';
}
```