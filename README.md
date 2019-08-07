# SilverStripe Foxy Discounts

A useful skeleton to more easily create modules that conform to the
[Module Standard](https://docs.silverstripe.org/en/developer_guides/extending/modules/#module-standard).

Offer discounts on purchase conditions for your Foxy products.

## Requirements

* SilverStripe ^4.0
* SilverStripe Foxy Products ^1.0

## Installation

```
composer require dynamic/silverstripe-foxy-discounts
```

## License
See [License](license.md)

## Example configuration

Add the following extensions to your product classes:

```yaml

Dynamic\Foxy\Model\Setting:
  extensions:
    - Dynamic\Foxy\Discounts\Extension\FoxyAdminExtension

Dynamic\Products\Page\Product:
  extensions:
    - Dynamic\Foxy\Discounts\Extension\ProductDataExtension

PageController:
  extensions:
    - Dynamic\Foxy\Discounts\Extension\PageControllerExtension
  
```

## Maintainers
*  [Dynamic](http://www.dynamicagency.com) (<dev@dynamicagency.com>)
 
## Bugtracker
Bugs are tracked in the issues section of this repository. Before submitting an issue please read over 
existing issues to ensure yours is unique. 
 
If the issue does look like a new bug:
 
 - Create a new issue
 - Describe the steps required to reproduce your issue, and the expected outcome. Unit tests, screenshots 
 and screencasts can help here.
 - Describe your environment as detailed as possible: SilverStripe version, Browser, PHP version, 
 Operating System, any installed SilverStripe modules.
 
Please report security issues to the module maintainers directly. Please don't file security issues in the bugtracker.
 
## Development and contribution
If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.
