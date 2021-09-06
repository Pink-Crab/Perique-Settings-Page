# Perique Settings Page

An extension for Perique Admin Menu module, part of the Perique Framework.

Allows for the creation of injectable settings classes and the generation of a matching settings page, which can be used as part of a custom Admin Menu group, or as a subpage to any other admin menu grouping.

![alt text](https://img.shields.io/badge/Current_Version-0.1.0-yellow.svg?style=flat " ")

 
[![Open Source Love](https://badges.frapsoft.com/os/mit/mit.svg?v=102)](https://github.com/ellerbrock/open-source-badge/)
![](https://github.com/Pink-Crab/Perique_Settings_Page/workflows/GitHub_CI/badge.svg " ")
[![codecov](https://codecov.io/gh/Pink-Crab/Perique_Settings_Page/branch/master/graph/badge.svg)](https://codecov.io/gh/Pink-Crab/Perique_Settings_Page)

## Installation

```bash
$ composer require pinkcrab/perique-settings-page
```

> Requires `pinkcrab/perique-admin-menu` module for the `Perique Framework`

### Depends on

* [PinkCrab Perique](https://github.com/Pink-Crab/Perqiue-Framework)
* [PinkCrab Perique Admin Menu](https://github.com/Pink-Crab/Perique_Admin_Menu)
* [PinkCrab Enqueue](https://github.com/Pink-Crab/Enqueue)
* [PinkCrab Form Fields](https://github.com/Pink-Crab/Form-Fields)

## Setup

There are a minimum of 2 classes which must be made to register a settings page and its matching settings object. The Settings class holds all of the settings details and the Page class is used to render the page as part of the Perique Admin Menu modules.

---
## Settings


The settings class, is extended form the `Abstract_Settings` class and has 3 methods which much be implemented.
---

### is_grouped(): bool

```php
/**
 * Denotes of the settings is grouped
 *
 * @return bool
 */
protected function is_grouped(): bool{
    // If the settings are saved as single items (add_option('key', 'value'));
    return false;

    // If the settings are saved as a grouped item (add_option('my_key', ['key'=>'value', 'key2'=>'value2'...]));
    return true;
}
```

> If you are planning to use a separate repository (opposed to WP_Options), its probably better to use this as single (return false)   

---

### group_key(): string

```php
/**
 * Denotes the group key (can be used a prefix for key, or the key all settings are saved under)
 *
 * @return string
 */
public function group_key(): string {
    return 'achme_settings';
}
```

> When used as grouped, this is the group key the settings are saved under.  
> When using single items, this is used as a prefix like `add_option('achme_settings_key', 'value)`

---

### fields(Setting_Collection $collection): Setting_Collection

```php
/**
 * Populates the settings group with all fields.
 *
 * @param \PinkCrab\Perique_Settings_Page\Setting\Setting_Collection $settings
 * @return \PinkCrab\Perique_Settings_Page\Setting\Setting_Collection
 */
protected function fields( Setting_Collection $settings): Setting_Collection{
    return $settings->push(
        Text::new( 'my_key' )
            ->set_label('Some Setting')
            ->set_description( 'This is its description' )
            ->set_data( 'foo', 'whatever is set here' ),
        Select::new( 'my_select' )
            ->set_label( 'Multiple Select' )
            ->set_option( 'A', 'Apple', 'Fruit' )
            ->set_option( 'B', 'Banana', 'Fruit' )
            ->set_option( 'F', 'Fish', 'Animal' )
            ->set_multiple(),
       //... Add as many fields as needed
    );
}
```

> There are a number of settings fields that can be implemented. These share a collection of methods/properties and also some additional which are specific to certain types.  
> [View all **Field Attributes**](docs/field-attributes.md)  
> [View all **Field Types**](docs/fields/readme.md)


---
## Page   

The settings page, needs a few properties defining, most of these are documented in the Admin_Menu module
---
g
```php
class My_Settings_Page extends Setting_Page{
	
    // Defined if a child page (See Admin_Menu Modules docs)
    protected $parent_slug = 'tools.php';

    // Denotes the page slug (See Admin_Menu Modules docs)
    protected $page_slug = 'my_settings_page';

    // Denotes the menu title (See Admin_Menu Modules docs)
    protected $menu_title = 'Achme Settings';

    // Denotes the pages title (See Admin_Menu Modules docs)
    protected $page_title = 'Achme Plugin Settings';

    // Denotes the menu position (See Admin_Menu Modules docs)
    protected $position = 1;

    // Returns the name of the class which holds our settings.
    public function settings_class_name(): string {
		return 'Namespace\My_Settings_Class'; // or My_Setting_Class:class
	}
}
```
---  

### settings_class_name(): string
> @return class-string<Abstract_Settings>  
> @required true (Is abstract method!)

```php
/**
 * Returns the class name for the settings.
 *
 * @return class-string<Abstract_Settings>
 */
protected function settings_class_name(): string{
    // Can be returned as the full string representation of the class name.
    return 'Namespace\My_Settings_Class';

    // Or using the ::class constant.
    return My_Settings_Class::class;
    );
}
```

> This is constructed via the DI container, so this allows the passing of dependencies to the settings class if required.

---

### enqueue_scripts(): ?Enqueue
> @return Enqueue|null  
> @required false  
> @default `return null;`

```php
/**
 * Returns any scripts that should be enqueued.
 *
 * @return Enqueue|null
 */
public function enqueue_scripts(): ?Enqueue {
    return Enqueue::script('my_scripts')
        ->src('path/to/file.js')
        ->deps('jquery');
}
```

> This should return an unregistered instance of the PinkCrab Enqueue object, please see [Enqueue](https://github.com/Pink-Crab/Enqueue) docs for more details.


---


### enqueue_styles(): ?Enqueue
> @return Enqueue|null  
> @required false  
> @default `return null;`

```php
/**
 * Returns any styles that should be enqueued.
 *
 * @return Enqueue|null
 */
public function enqueue_styles(): ?Enqueue {
    return Enqueue::style('my_styles')
        ->src('path/to/file.css');
}
```

> This should return an unregistered instance of the PinkCrab Enqueue object, please see [Enqueue](https://github.com/Pink-Crab/Enqueue) docs for more details.


## License ##

### MIT License ###

http://www.opensource.org/licenses/mit-license.html 

## Change Log ##

* 0.1.0 - Recreation of the old settings page module which made use of the native WP_Settings_API
