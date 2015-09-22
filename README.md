yii2-ip-validator
=================

Yii2 ip address validator. Can check that IP address is valid or in given range

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require alexeevdv/yii2-ip-validator "dev-master"
```

or add

```
"alexeevdv/yii2-ip-validator": "dev-master"
```

to the ```require``` section of your `composer.json` file.

## Usage

```php
public function rules() {
    return [
        //...
        ['ip', \alexeevdv\ip\Validator::className(), "range" => [
            "192.168.1.1", 
            "10.62.15.0/24", 
            "109.232.0.0/16"
        ]],
        //...
    ];
}

// or

$validator = new \alexeevdv\ip\Validator([
    "allowPrivate" => false,
    "allowReserved" => false,
]);

$validator->validate("127.0.0.1"); // false
```

## Params

```php

/**
 * Allow private ip addresses?
 * 10.0.0.0/8, 172.16.0.0/12 and 192.168.0.0/16
 */
bool $allowPrivate = true;

/**
 * Allow reserved ip addresses?
 * 0.0.0.0/8, 169.254.0.0/16, 192.0.2.0/24 and 224.0.0.0/4
 */
bool $allowReserved = true;

/**
 * Range of allowed ip addresses. Array of subnets and ip addresses.
 */
array $range = [];

```