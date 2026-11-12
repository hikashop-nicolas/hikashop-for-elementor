# HikaShop for Elementor

Design a [HikaShop](https://www.hikashop.com) store with [Elementor](https://elementor.com): product and category listings, the parts of the product page, cart, wishlist, currency switcher and filters as Elementor widgets, and HikaShop product pages rendered from an Elementor template. Works with the free Elementor.

The user documentation is in [readme.txt](readme.txt), the file WordPress.org shows.

## Requirements

- WordPress 6.6+, PHP 7.4+
- HikaShop 6.8.3+ on WordPress (from [hikashop.com](https://www.hikashop.com/download.html), the Starter edition is free)
- Elementor 3.24+

## How it works

The widgets do not reimplement any shop logic. Listings go through `hikashop_getLayout()`, the product page parts through HikaShop's own product blocks, and the cart, wishlist, currency and filter widgets through HikaShop's WordPress widgets. Prices, taxes, stock, access rights and the cart are HikaShop's, and HikaShop layout overrides in the theme apply.

The product page template uses the `hikashop_wp_page_output` filter of HikaShop. In the Elementor editor, HikaShop renders inside admin requests; `hikashop_siteRendering()` makes it render as on the site there.

## Development

```sh
bin/build.sh          # build/hikashop-for-elementor/ and its zip, without the dev files
```

CI lints the PHP on 7.4 to 8.4 and runs [Plugin Check](https://wordpress.org/plugins/plugin-check/) on the build, every category including experimental and low-severity checks, in strict mode: any error or warning fails the build.

## Release

1. Bump `Version` in `hikashop-for-elementor.php`, `HIKASHOP_ELEMENTOR_VERSION`, `Stable tag` in `readme.txt`, and add the changelog entry.
2. Tag the commit with the version (`1.0.1`) and push the tag. The deploy workflow checks the versions, runs Plugin Check, commits to the WordPress.org SVN and creates the GitHub release.

The SVN credentials are the `SVN_USERNAME` and `SVN_PASSWORD` repository secrets. WordPress.org assets (icon, banner, screenshots) go in `.wordpress-org/`.

## License

GPLv2 or later.
