RelayPoint gateway for Kiala
======

Kiala Relay point search

[![Latest Version](https://img.shields.io/github/release/pfeyssaguet/relaypoint-kiala.svg?style=flat-square)](https://github.com/pfeyssaguet/relaypoint-kiala/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/pfeyssaguet/relaypoint-kiala/master.svg?style=flat-square)](https://travis-ci.org/pfeyssaguet/relaypoint-kiala)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/pfeyssaguet/relaypoint-kiala.svg?style=flat-square)](https://scrutinizer-ci.com/g/pfeyssaguet/relaypoint-kiala/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/pfeyssaguet/relaypoint-kiala.svg?style=flat-square)](https://scrutinizer-ci.com/g/pfeyssaguet/relaypoint-kiala)

## Installation

Install via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "relaypoint/kiala": "~1.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Usage

``` php
$o = (new GatewayFactory())->create('Kiala');
$o->setParameter('dspid', 'DEMO_DSP'); // Replace this with your dspid

$a = $o->search(array('zip' => '92100'));

var_dump($a);
```

## Testing

``` bash
$ phpunit
```

## Credits

- [Pierre Feyssaguet](https://github.com/pfeyssaguet)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
