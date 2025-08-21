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

## Form options

``` bash

automatically_creatable: 
    if true, the form will be automatically created when the dossier is created in bulk from category
automatic_creation_checker_method: 
    if set, the form will check the target if the form is automatically creatable. 
    Ex. if you populate this parameter with 'isBornInEu' and you try to attach automatically this form to an Operator, 
    the operatro will call 'isBornInEu' method to check if the form is automatically creatable

```

## Datatable Fields

``` bash

        //link to dossiers by form. If the datatable row is a form, omit the form parameter
		'mySelfDossiers' => [
			'type' => 'filecabinet::dossiers.dossiersByForm',
			//ONLY IF row value is not a Form
			'form' => Form::getProjectClassName()::findCachedField('name', 'Patente')
		],

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
