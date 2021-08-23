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
> For the full Field list and there attributes, please see below.  

## Page

## Settings Fields

There are a number of settings fields that can be implemented. These share a collection of methods/properties and also some additional which are specific to certain types.

### *Shared Properties*

> All of the setters for properties return the current instance, so these can be used in a chained/fluent fashion

---

### protected string $key
This holds the key for the field, this is immutable and can only be set when creating a field.

```php
$field = new Field('my_key', 'type'); // Types are defined below
// Each type has a static constructor which can be used
$text = Text::new('my_key'); // same as new Field('my_key', 'text')
```
They key can be accessed using 
```php
$key = $field->get_key();
```

---

### protected string $type
This holds the type for the field, this is immutable and can only be set when creating a field.
```php
$field = new Field('my_key', 'number'); // This would create a number input <input type="number">

// You can get the field type using.
$type = $field->get_type(); // number.

// All Field types are populated with a TYPE constant, so they can also be accessed using.
$type = $field::TYPE; // number.
```

---

### protected string $label
Holds the label used for the setting.
```php
// To set the label
$field = Text::new('field')
    ->set_label('Some field');

// To get the label
$label = $field->get_label(); // Some field
```
---

### protected string|int|float|array|null $value
Holds the current value to the option.
```php
// This should not be set during the definition of the settings, as this is populated via the repository when being constructed by container. If you wish to set a default value, please see the sanitization callback below.
$field->set_value('Whatever is defined');

// Getting the current value.
$value = $field->get_value(); // Whatever is defined.
```

---

### protected $description
> @param string $description  
> @required false  
> @default ''

**Holds the description used for the setting.**

```php
// To set the description
$field = Text::new('field')
    ->set_description('This is my field, its magical');

// To get the description
$label = $field->get_description(); // This is my field, its magical
```
---


## Setting Field Types
### Text 
A basic `<input type="text">` field

> Implements: Placeholder, Data, Pattern

```php 
Text::new('setting_key')
```

## License ##

### MIT License ###

http://www.opensource.org/licenses/mit-license.html 

## Change Log ##
* 0.1.0 - Recreation of the old settings page module which made use of the native WP_Settings_API
