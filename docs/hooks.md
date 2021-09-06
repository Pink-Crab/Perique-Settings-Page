# Hooks

> There are several hooks, static and dynamic that are used throughout this libray.

---

We have a helper class, which makes using the hooks much easier. Each hook handle comes with a prefix of `pinkcrab/perique-settings/*` but this can be called using `PinkCrab\Perique_Settings_Page\Util\Hooks::HOOK_PREFIX` for ease.

> It is advisable to always use the defined constants below, to avoid any issues of hook handles changing in future.

## Default Page Render

> The default settings page renderer is included with the package and makes use of the PinkCrab/Form_Fields library. The rendered pages uses the following filters, if however you are creating your own implementation, you might not need these (although you can use them for more compatibility.)

### ELEMENT WRAPPER CLASS

Each element which is rendered is given a wrapper class, this can be moderated using the following filter.
`pinkcrab/perique-settings/element-wrapper-class` or calling `PinkCrab\Perique_Settings_Page\Util\Hooks::ELEMENT_WRAPPER_CLASS` . In the default Renderer implementation this is used to filter wrapper applied to the field.

```php
use PinkCrab\Perique_Settings_Page\Util\Hooks;

    add_filter(
        Hooks::ELEMENT_WRAPPER_CLASS, 
        /**
		 * @param string[]  $classes  Current wrapper classes.
		 * @param Field     $field    The current field being processed.
		 * @return string[] Wrapper classes.
         */
        function(array $classes, array $classes, Field $field): array {
            $classes[] = 'some-custom-wrapper';
            return $classes;
        },
        10,
        3
    );
```

### ELEMENT LABEL CLASS

Each element which is rendered is given a label, this can be moderated using the following filter.
`pinkcrab/perique-settings/element-label-class` or calling `PinkCrab\Perique_Settings_Page\Util\Hooks::ELEMENT_LABEL_CLASS` . In the default Renderer implementation this is used to filter label applied to the field.

```php
use PinkCrab\Perique_Settings_Page\Util\Hooks;

    add_filter(
        Hooks::ELEMENT_LABEL_CLASS, 
        /**
         * @param string  $class  The current classes
         * @param Field   $field  The field being rendered
         * @param Page    $page   The page being rendered
         * @return string The class to display
         */
        function(string $class, Field $field, Page $page): string {
            return 'acme-label';
        },
        10,
        3
    );
```

### ELEMENT INPUT CLASS

Each elements input field is rendered with classes based on the type and custom properties (select2).
`pinkcrab/perique-settings/element-input-class` or calling `PinkCrab\Perique_Settings_Page\Util\Hooks::ELEMENT_INPUT_CLASS` . In the default Renderer implementation this is used to filter the labels applied to the main input for the field.

```php
use PinkCrab\Perique_Settings_Page\Util\Hooks;

    add_filter(
        Hooks::ELEMENT_INPUT_CLASS, 
        /**
		 * Filters the element wrapper classes.
		 *
		 * @param string[]  $classes  Current wrapper classes.
		 * @param Field     $field    The current field being processed.
		 * @return string[] Wrapper classes.
		 */
        function(array $classes, Field $field): array {
            $classes[] = 'some-custom-class';
            return $classes;
        },
        10,
        2
    );
```

### PAGE GLOBAL SCRIPTS

Every Settings page that is rendered will make use of a global script which handles all JS based inputs (such as WP_Media). These scripts are enqueued using the `PinkCrab/Enqueue` library and through this filter can be replaced or amended as needed. Like the other filters, this can used as either the string literal or using the supplied helper class. `pinkcrab/perique-settings/page/global-script` or `PinkCrab\Perique_Settings_Page\Util\Hooks::PAGE_GLOBAL_SCRIPT`

```php
use PinkCrab\Enqueue\Enqueue;
use PinkCrab\Perique_Settings_Page\Util\Hooks;

    add_filter(
        Hooks::PAGE_GLOBAL_SCRIPT, 
        /**
		 * Filters the Enqueue object definition to manipulate the pages scripts
		 *
		 * @param Enqueue       $script  The current Enqueue class for the pages
		 * @param Setting_Page  $page    The current page.
		 * @return Enqueue|null Wrapper classes.
		 */
        function( Enqueue $script, Setting_Page $page ): ?Enqueue {
            
            // If page should have no scripts loaded.
            if($page->get_key() === 'my_key'){
                return null; // Will not enqueue any scripts for this specific page.
            }
            
            // As we use a fluent API, you can return when setting src (or any other properties.)
            return $script->src('some/custom/script.js')->footer(true); 
        }, 2, 10
    );
```


### PAGE GLOBAL STYLES

Like the global page scripts, there are custom styles which are applied to every page. Like the other filters, this can used as either the string literal or using the supplied helper class. `pinkcrab/perique-settings/page/global-style` or `PinkCrab\Perique_Settings_Page\Util\Hooks::PAGE_GLOBAL_STYLE`

```php
use PinkCrab\Enqueue\Enqueue;
use PinkCrab\Perique_Settings_Page\Util\Hooks;

    add_filter(
        Hooks::PAGE_GLOBAL_SCRIPT, 
        /**
		 * Filters the Enqueue object definition to manipulate the pages styles
		 *
		 * @param Enqueue       $style  The current Enqueue class for the pages
		 * @param Setting_Page  $page   The current page.
		 * @return Enqueue|null Wrapper classes.
		 */
        function( Enqueue $style, Setting_Page $page ): ?Enqueue {
            
            // If page should have no scripts loaded.
            if($page->get_key() === 'my_key'){
                return null; // Will not enqueue any default styles for this specific page.
            }
            
            // As we use a fluent API, you can return when setting the src (or any other properties.)
            return $style->src('some/custom/script.css'); 
        }, 2, 10
    );
```
