[![Build Status][0]][1]

randomhost/image
================

This package encapsulates some common GD library operations in PHP classes. It
was created as part of the `randomhost/webcamoverlay` package but is released as
a separate component so it can be used in other packages.

Because it was created as a dependency of the `randomhost/webcamoverlay` package,
it does only support a small subset of the available image handling functions.

Usage
-----

A basic approach at using this package could look like this:

```php
<?php
namespace randomhost\Image;

require_once '/path/to/vendor/autoload.php';

// load base image
$image = Image::getInstanceByPath('image.png');

// load overlay image
$overlay = Image::getInstanceByPath('overlay.png');

// insert overlay image on top of base image at x 15, y 20
$image->merge($overlay, 15, 20);

// setup a red text overlay
$text = new Text\Generic($image);
$text
    ->setTextFont('vera.ttf')
    ->setTextSize(12)
    ->setTextColor(
        new Color(
            0xFF,
            0x00,
            0x00
        )
    );

// setup a white border for the previously defined text overlay
$text = new Text\Decorator\Border($text);
$text->setBorderColor(
    new Color(
        0xFF,
        0xFF,
        0xFF
    )
);

// render text overlay onto the image at x 20, y 10
$text->insertText(
    20,
    10,
    'Example text'
);

// render the image
$image->render();
```

This will instantiate two image objects using image files from the file system,
merge the images and render an overlay text on top.

Assuming that you named this file `image.php`, you should now be able to access
the image at `http://example.com/image.php`

### The Image object

The `Image` object represents an image in a (remote) filesystem or created in
memory. It provides methods for retrieving information about the image and for
merging other `Image` instances.

#### Instantiation

There are two methods for creating an `Image` object instance:

1. `Image::getInstanceByPath($path, $cacheDir = '')`
   Creates an instance from an existing local or remote image file.
     - `$path`
     Path or URL to the image file.
     - `$cacheDir`
     Optional: Directory path for caching image files.
     This comes in handy when retrieving images from remote locations as caching
     them locally reduces the amount of HTTP requests which have to be made.
2. `Image::getInstanceByCreate($width, $height)`
     Creates an empty instance with the given image dimensions which can be used
     to merge (multiple) other `Image` instances into it.
     - `$width`
     Width of the generated image.
     - `$height`
     Height of the generated image.

#### Retrieving image data

The following public methods for retrieving image related data are available:

- `getMimetype()`
Returns the Mimetype of the image.

- `getModified()`
Returns the last modified timestamp of the image. When working with an instance
created with `getInstanceByCreate()`, this will be the time when the object was
initially created.

- `getWidth()`
Returns the width of the image in pixels.

- `getHeight()`
Returns the height of the image in pixels.

#### Combining images

- `merge(Image $srcImage, $dstX, $dstY, $strategy = self::MERGE_SCALE_SRC)`
Merges the image resource of the given `Image` instance into the image resource
of the active `Image` instance using the given coordinates and scaling strategy.

- `mergeAlpha(Image $srcImage, $dstX, $dstY, $alpha = 127)`
Merges the image resource of the given `Image` instance into the image resource
of the active `Image` instance using the given coordinates and alpha transparency.
This method does not support scaling.

#### Scaling strategies

- `Image::MERGE_SCALE_SRC`
This strategy uses width and height of the `Image` instance given to `merge()`.
If the image to be merged exceeds the dimensions of the target instance, it is
cropped to fit the dimensions of the target instance.

- `Image::MERGE_SCALE_DST`
This strategy re-sizes the `Image` instance given to `merge()` to match the
dimensions of the target instance. The x and y offset given to `merge()` will
however not be respected so the image to be merged may still be cropped.

- `Image::MERGE_SCALE_DST_NO_UPSCALE`
This strategy works similar to `Image::MERGE_SCALE_DST`, but does not upscale
the image to be merged if it is smaller than the target instance.

#### Rendering the image

- `render()`
Outputs the image stream to the browser. For now, images will always be rendered
as `image/png` to allow for full alpha-transparency support. Support for other
formats may be added in a later version.

### The Color object

The `Color` object is merely a data container for defining color values to be
used within the `PHP_Image` package. It comes with a set of setters and getters
for setting and retrieving color and alpha channel data.

#### Constructor

The constructor takes 4 parameters which are all optional:

- `$red`
Red component (0-255 or 0x00-0xFF).

- `$green`
Green component (0-255 or 0x00-0xFF).

- `$blue`
Blue component (0-255 or 0x00-0xFF).

- `$alpha`
Alpha value (0-127)

#### Configuring the color

- `setRed($red)`
Sets the red component (0-255 or 0x00-0xFF).

- `setGreen($green)`
Sets the green component (0-255 or 0x00-0xFF).

- `setBlue($blue)`
Sets the blue component (0-255 or 0x00-0xFF).

- `setAlpha($alpha)`
Sets the alpha value (0-127). 0 indicates completely opaque while 127 indicates
completely transparent.

#### Retrieving color data

- `getRed()`
Returns the red component (0-255 or 0x00-0xFF).

- `getGreen()`
Returns the green component (0-255 or 0x00-0xFF).

- `getBlue()`
Returns the blue component (0-255 or 0x00-0xFF).

- `getAlpha()`
Returns the alpha value (0-127). 0 indicates completely opaque while 127
indicates completely transparent.

#### Validating color data

- `Color::validateColor($color)`
Validates the color value and throws an InvalidArgumentException if the value is
invalid.

- `Color::validateAlpha($alpha)`
Validates the alpha value and throws an InvalidArgumentException if the value is
invalid.

### The Image/Text/Generic object

The `Image/Text/Generic` object is a generic text rendering object which can be
extended with additional functionality using decorators.

#### Constructor

The constructor takes 1 optional parameter:

- `$image`
An `Image` instance to operate on. This can also be set later using the
`setImage()` method.

#### Configuring the text

- `setImage(Image\Image $image)`
Sets the `Image` object instance.

- `setTextColor(Image\Color $color)`
Sets the `Color` used for rendering the text.

- `setTextFont($path)`
Sets the path to the font file used for rendering.

- `setTextSize($size)`
Sets the text size used for rendering.

#### Retrieving text data

- `getImage()`
Returns the `Image` object instance.

- `getTextColor()`
Returns the `Color` used for rendering the text.

- `getTextFont()`
Returns the path to the font file used for rendering.

- `getTextSize()`
Returns the text size used for rendering.

#### Rendering the text

- `insertText($xPosition, $yPosition, $text)`
Renders the given text onto the image resource, using the given coordinates.

### The Image/Text/Decorator object family

The `Image/Text/Decorator` object family contains decorators for text rendering
objects which add additional functionality to the `Image/Text/Generic` object or
other objects implementing the `Image\Text\Text` interface.

Since they wrap around any `Image\Text\Text` implementation which could also be
another `Image/Text/Decorator` instance, they share all methods of the decorated
object and might also define their own.

#### Constructor

The constructor depends on the specific `Image/Text/Decorator` implementation
but usually takes at least one parameter which is the object to decorate:

- `$text`
An `Image\Text\Text` implementation to decorate.

#### Retrieving text decorator data

- `providesMethod($name)`
Returns if the given method is implemented by one of the decorators in the
object tree as decorators can be stacked.

#### Rendering text using a decorator

Since all decorators implement the same interface as the standard
`Image/Text/Generic` object, rendering using decorated text objects works the
same way as rendering using non-decorated objects:

- `insertText($xPosition, $yPosition, $text)`
Renders the given text onto the image resource, using the given coordinates.

### The Image/Text/Decorator/Border object

The `Image/Text/Decorator/Border` object is a decorator for text rendering
objects which adds a simple border to the rendered text.

#### Configuring the border

- `setBorderColor(Image\Color $color)`
Sets the `Color` used for rendering the border.

#### Retrieving border data

- `getBorderColor()`
Returns the `Color` used for rendering the border.

License
-------

See LICENSE.txt for full license details.


[0]: https://travis-ci.org/randomhost/image.svg
[1]: https://travis-ci.org/randomhost/image
