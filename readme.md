# FileCabinet

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require ilbronza/filecabinet
```

## Usage

## Datatable Fields

``` bash

        //filecabinets list with populate/show link
        'rootFilecabinets' => 'filecabinet::filecabinets.filecabinetsStatus',

        //create a button to add filecabinets based on defined category
        'mySelfAddTest' => [
            'type' => 'filecabinet::attachForm.attachFormByCategory',
            'category' => Category::getProjectClassName()::findCachedField('name', 'Collaudo')
        ],

        //create a button to add filecabinets based on root filecabinets categories
        'mySelfAddTests' => 'filecabinet::attachForm.attachFormByRoots',

```



## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [author name][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ilbronza/filecabinet.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ilbronza/filecabinet.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ilbronza/filecabinet/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/ilbronza/filecabinet
[link-downloads]: https://packagist.org/packages/ilbronza/filecabinet
[link-travis]: https://travis-ci.org/ilbronza/filecabinet
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/ilbronza
[link-contributors]: ../../contributors
