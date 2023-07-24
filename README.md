# In developing...

Installation
------------
```sh
composer config repositories.romanzaytsev git https://github.com/RomanZaytsev/yii2-module-cms.git
composer require --prefer-dist romanzaytsev/yii2-module-cms "*"
```

Configuration
-------------

**Database Migrations**

```sh
php yii migrate --migrationPath=@vendor/romanzaytsev/yii2-module-cms/migrations
```

**Module Setup**

```php
$config['modules']['cms'] = [
    'class' => 'romanzaytsev\cms\Module',
];
$config['bootstrap'][] = 'cms';
```

**Check Installed Module**

```
http://localhost/cms
```
