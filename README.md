# WP Utilitatem

Utilitatem is a latin word meaning "utility". This project (WpUtm) is a set of utilities to setup the foundation of a WordPress project, be it plugin or theme.

## Initialization

You need to provide implementations of interfaces IDynamicJs and IDynamicCss, which are classes that will inject dynamic JS and CSS code into the page.

```php
$wputm = new \WpUtm\Main(
	array(
		'definitions' => array(
			\WpUtm\Interfaces\IDynamicCss::class => \DI\autowire(\YourPlugin\DynamicCss::class),
			\WpUtm\Interfaces\IDynamicJs::class => \DI\autowire(\YourPlugin\DynamicJs::class),
			'main_file' => YOUR_PLUGIN_FILE,
			'type' => 'plugin', // set theme or plugin here
			'prefix' => 'your_plugin_prefix'
		),
	)
);

$wputm->get( \YourPlugin\Main::class )->init();
```

## Registering assets.

WpUtm assumes that your JavaScript files will be in `build/js`, and css files in `build/css`.

It is recommended that you create your own `Assets.php` and inject `\WpUtm\AssetsRegistration` into it. Then simply call the `AssetsRegistration->register_scripts()` method.

### Adding footer scripts

By default, scripts are enqueued in the header. If you want a script to be enqueued in the footer, add its webpack entry point name within definitions `footer_scripts` array:

```
'footer_scripts' => array( 'my-script-1', 'my-script-2' )
```

## Attaching scripts and styles to Gutenberg blocks

Suppose that you want to enqueue a script every time the WordPress paragraph block is rendered on a page.

The name for the WordPress paragraph block is `core/paragraph`

In that case, you would need to create a webpack entry point called `core-paragraph`, and WP Utilitatem will automatically enqueue it whenever that block is rendered on the page.

Furthermore, if you create a file `core-paragraph.scss`, that style will be enqueued only if a paragraph block is rendered on the page.

## Roadmap

- Add conditions for when an asset attached to Gutenbeg block should be loaded (and also a filter)
- Add automatic enqueueing for scripts and styles based on the WordPress template system.
