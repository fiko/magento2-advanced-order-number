# Magento 2 Advanced Order Number

![Magento 2 Advanced Order Number](https://i.imgur.com/QThHzsf.png)

It's a magento 2 module to enable administrator to customise the order number (increment id).

## Demo Site

- Frontend Demo
[https://demo.dev.fiko.me/](https://demo.dev.fiko.me/)
- Backend Demo [https://demo.dev.fiko.me/admin](https://demo.dev.fiko.me/admin)
  - username: **demo_aon**
  - password: **demo123**

## How to install?

#### Via Composer

If you try to install via composer, just require your project to the module by running this command :

```
composer require fiko/magento2-advanced-order-number
```

#### Manually

1. Download this repo
2. Create a Directory `app/code/Fiko/AdvancedOrderNumber`
3. Copy downloaded repo to this directory

Once you download it (both composer or manually), just run this commands to apply this module to your project :

```
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

## How to use?

### Admin Guide

1. Login onto adminhtml.
2. go to menu of Stores > Settings > Configuration
3. go to section of FIKO > Advanced Order Number
4. You can start customising the order number

![Magento 2 Advanced Order Number - configuration](https://i.imgur.com/u2LjjVW.png)

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License

[MIT](COPYING.txt) &copy; 2023
