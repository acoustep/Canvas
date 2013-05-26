# Canvas

Canvas is a simple PHP class for creating and manipulating image sizes with the GD Library.

## Usage

### Setting up the canvas

There are two ways to set up the canvas.

1.  Through the constructor

```
$canvas = \Acoustep\Canvas(500, 250, '#000000');
```

2.  Through methods

```
$canvas = \Acoustep\Canvas()->width(500)->height(250)->background('#000000');
```

### Adding images

Images are added through the image method, you can add as many layers as you would like.  

The first image added will be added to the bottom of the output.  The rest will be layered on top of each other in the order they were added.

The image method takes two arguments: A string (The file along with the path to the file) and an array of arguments.

```
$canvas = \Acoustep\Canvas()->width(500)
							->height(250)
							->background('#000000')
							->image('images/test.jpg', array('x' => 'left',
											                 'y' => 'top',
											                 'scale' => 'best'));
```

X can be an integer which is the offset from the canvas in pixels or **left**, **centre** or **right**.

Y can also be an integer for a specific offset, **top**, **middle** or **bottom**

Scale can be **width**, **height**, **best** or **false**. 

* Width sets the image to fit best with the canvas width.
* Height sets the image to fit best with the canvas height.
* Best will try to figure out whether to use width or height for you.
* False will keep the image size the same as it was originally.

### Outputting the image

There are three methods to output the image to the user.  ```output()```, ```filetype()``` and ```create()```.

* Filetype lets you set the image type.  PNG, GIF and JPG are supported.
* Output lets you specify a filename to output the image to.  If ```NULL``` it will try to output to the current file - You will need to set the headers appropriately for it.
* Create is used when you are ready to output the image to the user. This method is always called last.

## Examples

### Output to a file named 'output.png'
```
$canvas = \Acoustep\Canvas()->width(500)
							->height(250)
							->background('#000000')
							->image('images/test.jpg', array('x' => 'left',
											                 'y' => 'top',
											                 'scale' => 'best'))
							->filetype('png')
							->output('output')
							->create();
```
### Output to the current page
```
header("Content-Type: image/png");
$canvas = \Acoustep\Canvas()->width(500)
							->height(250)
							->background('#000000')
							->image('images/test.jpg', array('x' => 'center',
											                 'y' => 'middle',
											                 'scale' => 'width'))
							->filetype('png')
							->output(NULL)
							->create();
```