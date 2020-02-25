# Shikiryu Backup ![language](https://img.shields.io/badge/language-php-blue.svg) ![issues](https://img.shields.io/github/issues-raw/Chouchen/Shikiryu_backup) ![ci](http://ci.canhelpme.com/build-status/image/1?branch=master&label=PHPCensor&style=flat-square)

> Because even small websites need to be backed up

Backup script for limited shared hosting

## :books: Table of Contents

- [Installation](#package-installation)
- [Usage](#rocket-usage)
- [Support](#hammer_and_wrench-support)
- [Contributing](#memo-contributing)
- [License](#scroll-license)

## :package: Installation

### First check if you have composer installed

Before installing this, you need to check if you have `PHP`and `Composer` installed on your computer.

### Then install this script

```sh
composer require shikiryu/backup
```

## :rocket: Usage

Everything in this library is based on a scenario (or multiple scenarii).

It consists of 2 sections in a JSON file : 
 * what to backup
 * where to backup
 
You have an example file in `app/scenario`.

The first section is `backup` (what to backup) and the second `transport`

More information about [how to use scenario](docs/using-scenario.md)


### Tips

Each possible section has docs in [their respective folders](docs/configuration)

Here is an example code 

```php
<?php

include_once 'vendor/autoload.php';

try {
    \Shikiryu\Backup\Scenario::launch('backup.json'); // whatever the file name you gave previously
} catch (\Exception $e) {
    echo $e->getMessage();
}
```

## :hammer_and_wrench: Support

Please [open an issue](https://github.com/Chouchen/Shikiryu_Backup/issues/new) for support.

## :memo: Contributing

Please contribute using [Github Flow](https://guides.github.com/introduction/flow/). Create a branch, add commits, and [open a pull request](https://github.com/Chouchen/Shikiryu_Backupleonard-henriquez/readme-boilerplateleonard-henriquez/readme-boilerplate/compare/).

## :scroll: License

[Creative Commons Attribution NonCommercial (CC-BY-NC)](https://tldrlegal.com/license/creative-commons-attribution-noncommercial-(cc-nc)) © [Chouchen](https://github.com/Chouchen/)
