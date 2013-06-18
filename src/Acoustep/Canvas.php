<?php
namespace Acoustep;

class Canvas
{
  /* Canvas width, height and background colour (hexadecimal) */
  public $width;
  public $height;
  public $background = '#000000';
  /* GD Library image resource */
  protected $image; 
  /* File type */
  public $filetype = 'png';
  /* Each image added is a "Layer" */
  protected $layers = array();
  /* File to output the image to - Use the current window if empty */
  protected $output = NULL;

  /**
   * construct()
   *
   * Optionally set the width, height and background colour of the image
   *
   * @width      integer   set the width in pixels
   * @height     integer   set the height in pixels
   * @background string    set the background colour in hexadecimal
   */
  public function __construct( $width=0, $height=0, $background='#000000' )
  {
    $this->width = $width;
    $this->height = $height;
    return $this;
  }

  /**
   * width()
   *
   * Set the width by itself.
   *
   * @w     integer  Set the width in pixels
   */
  public function width( $w )
  {
    $this->width = (int) $w;
    return $this;
  }
  
  /**
   * height()
   *
   * Set the height by itself.
   *
   * @h     integer  Set the height in pixels
   */
  public function height( $h )
  {
    $this->height = (int) $h;
    return $this;
  }

  /**
   * background()
   *
   * Set the background colour of the canvas
   *
   * @bg    string    Set the height of the background in hex
   */
  public function background( $bg )
  {
    $this->background = $bg;
    return $this;
  }


  /**
   * filetype()
   *
   * Set the file type to export to (if necessary)
   *
   * @ext   string    Set the extension of the image file without leading period)
   */
  public function filetype( $ext )
  {
    $this->filetype = $ext;
    return $this;
  }

  public function output( $output )
  {
    $this->output = ( !$output ) ? NULL : $output;
    return $this;
  }

  /**
   * image()
   *
   * Add an image to the canvas
   *
   * @file    string    The full URL to the image
   * @options array     An array of options for the image
   */
  public function image( $file, array $options )
  {
    $this->layers[] = array( 'file'    => $file,
                             'options' => $options );
    return $this;
  }

  /**
   * create()
   *
   * Create the image with all the settings specified 
   */
  public function create()
  {
    $this->image = ImageCreateTrueColor( $this->width, $this->height );
    $colour = $this->convert_hex_to_rgb( $this->background );
    $background = imagecolorallocate( $this->image, $colour['red'], $colour['green'], $colour['blue'] );

    $this->add_layers_to_canvas();

    if( $this->output )
      $this->output .= '.'.$this->filetype;

    switch( $this->filetype )
    {
      case 'jpg':
      case 'jpeg':
        imagejpeg( $this->image, $this->output );
        break;
      case 'gif':
        imagegif( $this->image, $this->output );
        break;
      default:
        imagepng( $this->image, $this->output );
    }
    imagedestroy($this->image);
  }

  /**
   * add_layers_to_canvas
   *
   * Takes each layer and adds them on top of eachother
   */
  private function add_layers_to_canvas()
  {
    foreach( $this->layers as $layer )
    {
      $options['x'] = ( isset($layer['options']['x']) ) ? $layer['options']['x'] : 0;
      $options['y'] = ( isset($layer['options']['y']) ) ? $layer['options']['y'] : 0;
      $options['scale'] = ( $layer['options']['scale'] ) ? $layer['options']['scale'] : false;

      list( $image_width, $image_height, $image_type, $image_attr ) = getimagesize( $layer['file'] );

      $layer_dimensions = $this->get_image_dimensions( $layer['options']['scale'], $image_width, $image_height );


      $left = $this->get_x_offset( $options['x'], $layer_dimensions[0], $layer_dimensions[1], $layer['options']['x'] );
      $top = $this->get_y_offset( $options['y'], $layer_dimensions[0], $layer_dimensions[1], $layer['options']['y'] );
      imagecopyresampled( $this->image,
                        $this->get_file_resource( $layer['file'], $image_type ),
                        $left,
                        $top,
                        0,
                        0,
                        $layer_dimensions[0],
                        $layer_dimensions[1],
                        $image_width,
                        $image_height);
    }
  }

  /**
   * get_y_offset()
   *
   * Find the y axis offset in pixels depending on what the user specifies
   *
   * @y                  integer/string  Can be an integer to offset in pixels, middle, top or bottom.
   * @destination_width  integer         Width of the image being added to Canvas after being scaled
   * @destination_height integer         Height of the image being added to Canvas after being scaled
   */
  private function get_y_offset( $y=0, $destination_width, $destination_height )
  {
    if($y === 0)
      $y = 'top';

    switch( $y )
    {
      case "top":
        return 0;
        break;
      case "middle":
        return ( $this->height - $destination_height ) / 2;
        break;
      case "bottom":
        return $this->height - $destination_height;
        break;
      default:
        return (int) $y;
    }
}

  /**
   * get_x_offset()
   *
   * Find the x axis offset in pixels depending on what the user specifies
   *
   * x                   integer/string  Can be an integer to offset in pixels, left, centre/center or right.
   * @destination_width  integer         Width of the image being added to Canvas after being scaled
   * @destination_height integer         Height of the image being added to Canvas after being scaled
   */
  private function get_x_offset( $x=0, $destination_width, $destination_height )
  {
    if($x === 0)
      $x = 'left';

    switch( $x )
    {
      case 'left':
      case 0:
        return 0;
        break;
      case 'center':
      case 'centre':
        return ( $this->width - $destination_width ) / 2;
        break;
      case 'right':
        return $this->width - $destination_width;
        break;
      default:
        return (int) $x;
    }
  }

  /**
   * get_image_dimensions()
   *
   * Scale the image being added to fit onto the canvas how the user prefers
   *
   * @scale        integer/string    scale can be best (figures out best fit), width (scale to width of canvas) or height (scale to height of canvas)
   * @image_width  integer           The original width of the image being added
   * @image_height integer           The original height of the image being added
   * */
  private function get_image_dimensions( $scale, $image_width, $image_height )
  {
    switch( $scale )
    {
      case 'width':
        list( $destination_width, $destination_height ) = $this->use_image_width( $image_width, $image_height );
        break;
      case 'height':
        list( $destination_width, $destination_height) = $this->use_image_height( $image_width, $image_height );
        break;
      case 'best':
        $vertical_space = $this->height - $image_height;
        $horizontal_space = $this->width - $image_width;

        if( $vertical_space >= $horizontal_space )
          list( $destination_width, $destination_height ) = $this->use_image_height( $image_width, $image_height );
        else
          list( $destination_width, $destination_height ) = $this->use_image_width( $image_width, $image_height );
        break;
      default:
        $destination_width = $image_width;
        $destination_height = $image_height;
    }
    return array( $destination_width,
                  $destination_height);
  }
  /**
   * use_image_height()
   *
   * Find the new width and height of the images being added using the height for best fit
   *
   * @image_width     integer   The original image's width
   * @image_height    integer   The original image's height
   * */
  private function use_image_height( $image_width, $image_height )
  {
    $destination_height = $this->height;
    $ratio = $this->height / $image_height;
    $destination_width = $image_width * $ratio;
    return array( (int) $destination_width,
                  (int) $destination_height);
  }
  /**
   * use_image_width()
   *
   * Find the new width and height of the images being added using the width for best fit
   *
   * @image_width     integer   The original image's width
   * @image_height    integer   The original image's height
   * */
  private function use_image_width( $image_width, $image_height )
  {
    $destination_width = $this->width;
    $ratio = $this->width / $image_width;
    $destination_height = $image_height * $ratio;
    return array( (int) $destination_width,
                  (int) $destination_height);

  }

  /**
   * get_file_resource()
   *
   * Get the image being added depending on the image type
   *
   * @file       string  The filename (including the path)
   * @image_type integer The file type
   */
  private function get_file_resource( $file, $image_type )
  {

    switch( $image_type )
    {
      case 1:
        $p = imagecreatefromgif( $file );
        break;
      case 2:
        $p = imagecreatefromjpeg( $file );
        break;
      case 3:
        $p = imagecreatefrompng( $file );
        break;
    }
    return $p;
  }

  /**
   * convert_hex_to_rgb()
   *
   * Convert hex value to RGB for creating colours with the GD Library
   *
   * @hex   string    A hexadecimal value to be converted to RGB format
   */
  private function convert_hex_to_rgb( $hex )
  {
    if( substr( $hex, 0, 1 ) == '#' )
      $hex = substr( $hex, 1 );

    if( strlen( $hex ) == 3 )
    {
      $t = str_split( $str );
      $hex = $t[0].$t[0].$t[1].$t[1].$t[2].$t[2];
    }
    $R = hexdec( substr( $hex,0,2 ) );
    $G = hexdec( substr( $hex,2,4 ) );
    $B = hexdec( substr( $hex,4,6 ) );
    return array( 'red' => $R,
                  'green' => $G,
                  'blue' => $B );
  }
}
