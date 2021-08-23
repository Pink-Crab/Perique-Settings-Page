## Field Attributes

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