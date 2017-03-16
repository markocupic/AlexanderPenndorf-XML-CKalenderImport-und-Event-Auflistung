# Haste Ajax\ReloadHelper

This utility class is designed to help handle the ajax reload of frontend modules and content elements.  


## Usage

First of all you need to subscribe your frontend module or content element to the events: 

```php
// Content element
\Haste\Ajax\ReloadHelper::subscribe(
    \Haste\Ajax\ReloadHelper::getUniqid(\Haste\Ajax\ReloadHelper::TYPE_CONTENT, $this->id),
    ['foo-event']
);

// Frontend module
\Haste\Ajax\ReloadHelper::subscribe(
    \Haste\Ajax\ReloadHelper::getUniqid(\Haste\Ajax\ReloadHelper::TYPE_MODULE, $this->id),
    ['bar-event']
);

// Custom
\Haste\Ajax\ReloadHelper::subscribe('my-unique-id', ['baz-event']);
```

Then in your markup you can refresh the subscribed modules by firing the specific event:

```html
<a href="#" id="refresh">Refresh the module</a>

<script>
document.getElementById('refresh').addEventListener('click', function (e) {
    e.preventDefault();
    HasteAjaxReload.dispatchEvents('foo-event', 'bar-event');
});
</script>
```


## Notes

The utility comes with the library-agnostic JavaScript file that is added to the page
when there is at least one listener registered. 

The frontend module and content element in its markup must have a single wrapper element
which is usually the case with `<div>` container with `ce_` or `mod_` classes
but it also works on other wrapping elements such as `<section>` etc.
