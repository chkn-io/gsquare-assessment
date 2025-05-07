# Shopify Developer Technical Challenge  
_From Percian Joseph Borja_

---

## Bonus Question: Loyalty App Strategy

If I were building a custom Shopify app to hook into a third-party loyalty system, I’d kick things off by setting up the usual OAuth flow so we can securely get the merchant’s access token. That token’s our golden key to talk to the Shopify Admin API. From there, I’d wire up webhooks like `orders/create` and `customers/create` so we can catch events in real-time — every time someone makes a purchase or signs up, we shoot that data over to the loyalty service and keep things in sync.

For the merchant side, I’d build a clean admin interface using Polaris and App Bridge so it feels native inside Shopify. They’d be able to tweak settings, see points activity, and maybe trigger manual syncs if needed. On the backend, I’d queue up API calls just to keep things smooth and avoid timeouts. And of course, I’d make sure everything’s secure with webhook verification and encrypted tokens. Nothing too fancy — just clean, fast, and solid.

---

## 1. Product Page Enhancements

- **Sale Badge (% OFF)**  
  I removed the default "Sale" badge and replaced it with a dynamic "N% OFF" badge directly inside `price.liquid`.

- **Low Stock Warning**  
  If the inventory is less than 5, a “Low stock – order soon!” message appears below the price and sale badge.

- **Add to Cart Bonus (Free Product Over $150)**  
  A JavaScript snippet checks the cart subtotal. If it exceeds $150, a free product (using the provided product ID) is automatically added. It’s added only once and removed if the cart total drops below $150.

---

## 2. Collection Page Enhancements

- **Custom Filters Using Tags**  
  I used Shopify’s **Search & Discovery** app to create tag-based filters (e.g., by Color).

- **Lazy-Loading Images**  
  Added `loading="lazy"` to product images in the collection template to improve page performance.

---

## 3. 🛒 Cart Page – Gift Wrap Option

Added a checkbox labeled “Add gift wrap ($5)”. When selected, it adds a separate "Gift Wrap" product to the cart. When unchecked, the product is removed.  
The checkbox is also auto-checked on reload if the item is already in the cart and displays a "Please wait…" label while processing.

---

## 4. Webhook Integration (`orders/create`)

I created a simple `webhook.php` listener for the `orders/create` webhook event. It does the following:

- Reads the raw JSON from the POST body
- Extracts and logs:
  - Order ID
  - Customer email
  - Total price
  - Line items (product name + quantity)
- Writes the info to a local file: `orders_log.json`
- Sends a `200 OK` response back to Shopify to confirm it was received

This is a clean, no-database setup perfect for testing or light usage.

---

## 5. Admin API Integration (Metafields)

### Endpoint:
path/to/project/webhoost_and_api/update_metafield.php?value=[any_value_you_want_to_save]



This PHP script is a quick API endpoint I built to update the value of a product metafield in Shopify — specifically custom.warehouse_location. You can pass the new value directly through the URL using ?value=your_value, which makes it easy to test or trigger from a browser or script.

When the script runs, it first pulls all existing metafields for the given product (using the Admin API). It then loops through them to find the one with the key warehouse_location inside the custom namespace. Once it has that metafield's ID, it sends a PUT request to update its value to whatever was passed in via the query string. If no value is given, it defaults to "abc".

The final response confirms the status with the HTTP code and shows the updated value 


Before running update_metafield.php, make sure to update the following variables inside the script:

$store = '';           // Your Shopify store domain (e.g., 'your-store.myshopify.com' — no https://)
$accessToken = '';     // Your Admin API access token (found in Shopify > Apps > Your App > API Credentials)
$productId = '';       // The product ID you want to update the metafield for


# Dawn

[![Build status](https://github.com/shopify/dawn/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/Shopify/dawn/actions/workflows/ci.yml?query=branch%3Amain)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?color=informational)](/.github/CONTRIBUTING.md)

[Getting started](#getting-started) |
[Staying up to date with Dawn changes](#staying-up-to-date-with-dawn-changes) |
[Developer tools](#developer-tools) |
[Contributing](#contributing) |
[Code of conduct](#code-of-conduct) |
[Theme Store submission](#theme-store-submission) |
[License](#license)

Dawn represents a HTML-first, JavaScript-only-as-needed approach to theme development. It's Shopify's first source available theme with performance, flexibility, and [Online Store 2.0 features](https://www.shopify.com/partners/blog/shopify-online-store) built-in and acts as a reference for building Shopify themes.

* **Web-native in its purest form:** Themes run on the [evergreen web](https://www.w3.org/2001/tag/doc/evergreen-web/). We leverage the latest web browsers to their fullest, while maintaining support for the older ones through progressive enhancement—not polyfills.
* **Lean, fast, and reliable:** Functionality and design defaults to “no” until it meets this requirement. Code ships on quality. Themes must be built with purpose. They shouldn’t support each and every feature in Shopify.
* **Server-rendered:** HTML must be rendered by Shopify servers using Liquid. Business logic and platform primitives such as translations and money formatting don’t belong on the client. Async and on-demand rendering of parts of the page is OK, but we do it sparingly as a progressive enhancement.
* **Functional, not pixel-perfect:** The Web doesn’t require each page to be rendered pixel-perfect by each browser engine. Using semantic markup, progressive enhancement, and clever design, we ensure that themes remain functional regardless of the browser.

You can find a more detailed version of our theme code principles in the [contribution guide](https://github.com/Shopify/dawn/blob/main/.github/CONTRIBUTING.md#theme-code-principles).

## Getting started
We recommend using Dawn as a starting point for theme development. [Learn more on Shopify.dev](https://shopify.dev/themes/getting-started/create).

> If you're building a theme for the Shopify Theme Store, then you can use Dawn as a starting point. However, the theme that you submit needs to be [substantively different from Dawn](https://shopify.dev/themes/store/requirements#uniqueness) so that it provides added value for merchants. Learn about the [ways that you can use Dawn](https://shopify.dev/themes/tools/dawn#ways-to-use-dawn).

Please note that the main branch may include code for features not yet released. The "stable" version of Dawn is available in the theme store.

## Staying up to date with Dawn changes

Say you're building a new theme off Dawn but you still want to be able to pull in the latest changes, you can add a remote `upstream` pointing to this Dawn repository.

1. Navigate to your local theme folder.
2. Verify the list of remotes and validate that you have both an `origin` and `upstream`:
```sh
git remote -v
```
3. If you don't see an `upstream`, you can add one that points to Shopify's Dawn repository:
```sh
git remote add upstream https://github.com/Shopify/dawn.git
```
4. Pull in the latest Dawn changes into your repository:
```sh
git fetch upstream
git pull upstream main
```

## Developer tools

There are a number of really useful tools that the Shopify Themes team uses during development. Dawn is already set up to work with these tools.

### Shopify CLI

[Shopify CLI](https://github.com/Shopify/shopify-cli) helps you build Shopify themes faster and is used to automate and enhance your local development workflow. It comes bundled with a suite of commands for developing Shopify themes—everything from working with themes on a Shopify store (e.g. creating, publishing, deleting themes) or launching a development server for local theme development.

You can follow this [quick start guide for theme developers](https://shopify.dev/docs/themes/tools/cli) to get started.

### Theme Check

We recommend using [Theme Check](https://github.com/shopify/theme-check) as a way to validate and lint your Shopify themes.

We've added Theme Check to Dawn's [list of VS Code extensions](/.vscode/extensions.json) so if you're using Visual Studio Code as your code editor of choice, you'll be prompted to install the [Theme Check VS Code](https://marketplace.visualstudio.com/items?itemName=Shopify.theme-check-vscode) extension upon opening VS Code after you've forked and cloned Dawn.

You can also run it from a terminal with the following Shopify CLI command:

```bash
shopify theme check
```

### Continuous Integration

Dawn uses [GitHub Actions](https://github.com/features/actions) to maintain the quality of the theme. [This is a starting point](https://github.com/Shopify/dawn/blob/main/.github/workflows/ci.yml) and what we suggest to use in order to ensure you're building better themes. Feel free to build off of it!

#### Shopify/lighthouse-ci-action

We love fast websites! Which is why we created [Shopify/lighthouse-ci-action](https://github.com/Shopify/lighthouse-ci-action). This runs a series of [Google Lighthouse](https://developers.google.com/web/tools/lighthouse) audits for the home, product and collections pages on a store to ensure code that gets added doesn't degrade storefront performance over time.

#### Shopify/theme-check-action

Dawn runs [Theme Check](#Theme-Check) on every commit via [Shopify/theme-check-action](https://github.com/Shopify/theme-check-action).

## Contributing

Want to make commerce better for everyone by contributing to Dawn? We'd love your help! Please read our [contributing guide](https://github.com/Shopify/dawn/blob/main/.github/CONTRIBUTING.md) to learn about our development process, how to propose bug fixes and improvements, and how to build for Dawn.

## Code of conduct

All developers who wish to contribute through code or issues, please first read our [Code of Conduct](https://github.com/Shopify/dawn/blob/main/.github/CODE_OF_CONDUCT.md).

## Theme Store submission

The [Shopify Theme Store](https://themes.shopify.com/) is the place where Shopify merchants find the themes that they'll use to showcase and support their business. As a theme partner, you can create themes for the Shopify Theme Store and reach an international audience of an ever-growing number of entrepreneurs.

Ensure that you follow the list of [theme store requirements](https://shopify.dev/themes/store/requirements) if you're interested in becoming a [Shopify Theme Partner](https://themes.shopify.com/services/themes/guidelines) and building themes for the Shopify platform.

## License

Copyright (c) 2021-present Shopify Inc. See [LICENSE](/LICENSE.md) for further details.
