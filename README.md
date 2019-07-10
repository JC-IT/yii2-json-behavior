# JSON behavior for Yii2

This extension provides a package that implements magic getters and setters based on a list of configured attributes. 
Ideal for single table inheritance.

```bash
$ composer require jc-it/yii2-json-behavior
```

or add

```
"jc-it/yii2-json-behavior": "^<latest version>"
```

to the `require` section of your `composer.json` file.

## Configuration

In a model:

```php
/**
 * @return array
 */
public function behaviors(): array
{
    return ArrayHelper::merge(
        parent::behaviors(),
        [
            JsonConfigurationBehavior::class => [
                'class' => JsonConfigurationBehavior::class,
                '<jsonAttribute>' => [
                    '<attribute>' => '<defaultValue>'
                ] 
            ],
        ]
    );
}
```

## Credits
- [Joey Claessen](https://github.com/joester89)

## License

The MIT License (MIT). Please see [LICENSE](https://github.com/jc-it/yii2-json-configuration-behavior/blob/master/LICENSE) for more information.