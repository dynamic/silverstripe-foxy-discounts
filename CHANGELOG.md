# Changelog

## [1.0.5](https://github.com/dynamic/silverstripe-foxy-discounts/tree/1.0.5) (2019-11-18)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy-discounts/compare/1.0.4...1.0.5)

**Implemented enhancements:**

- DOCS allow for excluding products from discounts [\#34](https://github.com/dynamic/silverstripe-foxy-discounts/issues/34)
- FEATURE move Discounts from Foxy admin to separate ModelAdmin [\#33](https://github.com/dynamic/silverstripe-foxy-discounts/issues/33)

**Fixed bugs:**

- BUG README requirements list foxy-products, not a requirement [\#36](https://github.com/dynamic/silverstripe-foxy-discounts/issues/36)

**Merged pull requests:**

- BUGFIX make it easier to check if discountable [\#41](https://github.com/dynamic/silverstripe-foxy-discounts/pull/41) ([muskie9](https://github.com/muskie9))
- BUGFIX README requirements [\#38](https://github.com/dynamic/silverstripe-foxy-discounts/pull/38) ([jsirish](https://github.com/jsirish))
- FEATURE DiscountAdmin [\#37](https://github.com/dynamic/silverstripe-foxy-discounts/pull/37) ([jsirish](https://github.com/jsirish))
- DOCS advanced usage for Discount restrictions [\#35](https://github.com/dynamic/silverstripe-foxy-discounts/pull/35) ([muskie9](https://github.com/muskie9))

## [1.0.4](https://github.com/dynamic/silverstripe-foxy-discounts/tree/1.0.4) (2019-10-30)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy-discounts/compare/1.0.3...1.0.4)

**Fixed bugs:**

- BUG DiscountTier::getDiscountLabel\(\) not extensible [\#29](https://github.com/dynamic/silverstripe-foxy-discounts/issues/29)

**Merged pull requests:**

- BUGFIX Jenkinsfile - fail test if phpcs doesn’t pass [\#32](https://github.com/dynamic/silverstripe-foxy-discounts/pull/32) ([jsirish](https://github.com/jsirish))
- BUGFIX Travis phpcs tests [\#31](https://github.com/dynamic/silverstripe-foxy-discounts/pull/31) ([jsirish](https://github.com/jsirish))
- BUGFIX allow extensible DiscountLabel [\#30](https://github.com/dynamic/silverstripe-foxy-discounts/pull/30) ([muskie9](https://github.com/muskie9))

## [1.0.3](https://github.com/dynamic/silverstripe-foxy-discounts/tree/1.0.3) (2019-10-28)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy-discounts/compare/1.0.2...1.0.3)

**Merged pull requests:**

- BUGFIX discount detection broken [\#28](https://github.com/dynamic/silverstripe-foxy-discounts/pull/28) ([muskie9](https://github.com/muskie9))

## [1.0.2](https://github.com/dynamic/silverstripe-foxy-discounts/tree/1.0.2) (2019-09-24)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy-discounts/compare/1.0.1...1.0.2)

**Fixed bugs:**

- BUG not providing discount price as DBCurrency [\#26](https://github.com/dynamic/silverstripe-foxy-discounts/issues/26)
- BUG .standard-price reference in discount.js not strict enough [\#24](https://github.com/dynamic/silverstripe-foxy-discounts/issues/24)

**Merged pull requests:**

- BUGFIX $discounted\_price now DBCurrency [\#27](https://github.com/dynamic/silverstripe-foxy-discounts/pull/27) ([muskie9](https://github.com/muskie9))
- BUGFIX look for .standard-price within the form [\#25](https://github.com/dynamic/silverstripe-foxy-discounts/pull/25) ([muskie9](https://github.com/muskie9))

## [1.0.1](https://github.com/dynamic/silverstripe-foxy-discounts/tree/1.0.1) (2019-09-24)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy-discounts/compare/1.0.0...1.0.1)

**Fixed bugs:**

- BUG getBestDiscount\(\) may carry over previous values [\#22](https://github.com/dynamic/silverstripe-foxy-discounts/issues/22)

**Merged pull requests:**

- BUGFIX add getHasDiscount\(\) that compares product information [\#23](https://github.com/dynamic/silverstripe-foxy-discounts/pull/23) ([muskie9](https://github.com/muskie9))

## [1.0.0](https://github.com/dynamic/silverstripe-foxy-discounts/tree/1.0.0) (2019-09-23)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy-discounts/compare/858aa2d00604cc9bc7f63dec9fef2627e3df9c56...1.0.0)

**Implemented enhancements:**

- ENHANCEMENT order total display to be configurable [\#19](https://github.com/dynamic/silverstripe-foxy-discounts/issues/19)
- FEATURE Don't hard code Discounts to silverstripe-products Product class [\#13](https://github.com/dynamic/silverstripe-foxy-discounts/issues/13)
- FEATURE JS to update Price in AddToCartForm based on quantity field value [\#10](https://github.com/dynamic/silverstripe-foxy-discounts/issues/10)
- FEATURE Discount has\_many DiscountTiers [\#3](https://github.com/dynamic/silverstripe-foxy-discounts/issues/3)

**Fixed bugs:**

- BUG ProductDataExtension - getActiveDiscount\(\) sorting [\#14](https://github.com/dynamic/silverstripe-foxy-discounts/issues/14)
- BUG Trigger field does not check if the trigger amount has already been entered [\#15](https://github.com/dynamic/silverstripe-foxy-discounts/issues/15)

**Closed issues:**

- FEATURE Allow discount to be either percentage \(current\) or amount \(new\) [\#7](https://github.com/dynamic/silverstripe-foxy-discounts/issues/7)

**Merged pull requests:**

- BUGFIX DiscountTier Quantity validation [\#21](https://github.com/dynamic/silverstripe-foxy-discounts/pull/21) ([muskie9](https://github.com/muskie9))
- ENHANCEMENT allow for config of price type [\#20](https://github.com/dynamic/silverstripe-foxy-discounts/pull/20) ([muskie9](https://github.com/muskie9))
- BUGFIX account for non-product pages [\#18](https://github.com/dynamic/silverstripe-foxy-discounts/pull/18) ([muskie9](https://github.com/muskie9))
- ENHANCEMENT DiscountHelper [\#17](https://github.com/dynamic/silverstripe-foxy-discounts/pull/17) ([muskie9](https://github.com/muskie9))
- ENHANCEMENT FE discount price update [\#16](https://github.com/dynamic/silverstripe-foxy-discounts/pull/16) ([muskie9](https://github.com/muskie9))
- REFACTOR requrie silverstripe foxy, not silverstripe-foxy-products [\#12](https://github.com/dynamic/silverstripe-foxy-discounts/pull/12) ([jsirish](https://github.com/jsirish))
- FEATURE Allow discounts by amount [\#11](https://github.com/dynamic/silverstripe-foxy-discounts/pull/11) ([jsirish](https://github.com/jsirish))
- FEATURE DiscountTier [\#9](https://github.com/dynamic/silverstripe-foxy-discounts/pull/9) ([jsirish](https://github.com/jsirish))
- refactor - remove extensions from config [\#8](https://github.com/dynamic/silverstripe-foxy-discounts/pull/8) ([jsirish](https://github.com/jsirish))
- BUGFIX check if class hasMethod getActiveDiscount [\#6](https://github.com/dynamic/silverstripe-foxy-discounts/pull/6) ([jsirish](https://github.com/jsirish))
- FEATURE apply discounts to products [\#5](https://github.com/dynamic/silverstripe-foxy-discounts/pull/5) ([jsirish](https://github.com/jsirish))
- BUGFIX spelling error in composer name [\#4](https://github.com/dynamic/silverstripe-foxy-discounts/pull/4) ([jsirish](https://github.com/jsirish))
- FEATURE Discount initial build [\#2](https://github.com/dynamic/silverstripe-foxy-discounts/pull/2) ([jsirish](https://github.com/jsirish))
- Composer and README updates [\#1](https://github.com/dynamic/silverstripe-foxy-discounts/pull/1) ([jsirish](https://github.com/jsirish))



\* *This Changelog was automatically generated by [github_changelog_generator](https://github.com/github-changelog-generator/github-changelog-generator)*
