<?php
namespace Acoustep;

class Canvas
{
	public $width;
	public $height;
	public $output;
	public $background = '#ffffff';
	public $image = false;
	public $type = 'png';
	public $scale = false;
	public $shapes = array();
  public $i;
  public $layers = array();
	/**
	 * Initiate the canvas
	 * @param integer $width  set the width
	 * @param integer $height set the height
	 */
	public function __construct($width=0, $height=0)
	{
		$this->width = $width;
		$this->height = $height;

		return $this;
	}

	public function width($x)
	{
		$this->width = $x;
		return $this;
	}
	public function height($x)
	{
		$this->height = $x;
		return $this;
	}
	public function output($x)
	{
		$this->output = $x;
		return $this;
	}
	public function background($x)
	{
		$this->background = $x;
		return $this;
	}
	public function type($x)
	{
		$this->type = $x;
		return $this;
	}
	public function image($filename, array $options)
	{
    // TO DO
    $this->layers[] = array('type' => 'image',
                            'value' => $filename,
                            'options' => $options);
		return $this;
	}
	
  public function add($type, $value, array $options)
  {
    switch($type)
    {
      case 'shape':
          $this->shape($value, $options);
        break;
      case 'image':
        $this->image($value, $options);
        break;
      case 'text':
          // TO DO
        break;
    }
    return $this;
  }
	public function shape($shape, array $options)
	{
		/* 
		 * shape
		 * colour
		 * transparency
		 * x
		 * y
		 * w
		 * h
		 */
    $options['type'] = 'shape';
    $options['value'] = $shape;
		$this->layers[] = $options;
		return $this;
	}
	/**
	 * Create image
	 * @return [type] [description]
	 */
	public function create()
	{
		$this->i = null;
		$this->i = ImageCreateTrueColor( $this->width, $this->height );
		$colour = $this->convert_hex_to_rgb($this->background);

		$background = imagecolorallocate( $this->i, $colour['red'] , $colour['green'], $colour['blue']);

		foreach($this->layers as $layer)
    {
      switch($layer['type'])
      {
        case 'image':
          $this->insert_image($layer['value'], $layer['options']);
          break;
        case 'text':
          $this->insert_text($layer['value'], $layer['options']);
          break;
        case 'shape':
          $this->insert_shape($layer);
          break;
      }
		}

		switch($this->type)
		{
			case 'jpg':
			case 'jpeg':
				imagejpeg($this->i,$this->output.'.'.$this->type);
				break;
			case 'png':
				imagepng($this->i,$this->output.'.'.$this->type);
				break;
			case 'gif':
				imagegif($this->i,$this->output.'.'.$this->type);
				break;
		}

		imagedestroy($this->i);

		return $this;
	}

  private function insert_image($filename, array $options)
  {
    $options['x'] = (isset($options['x'])) ? $options['x'] : 0;
    $options['y'] = (isset($options['y'])) ? $options['y'] : 0;
    $options['scale'] = (isset($options['scale'])) ? $options['scale'] : false;

    list($image_width, $image_height, $image_type, $image_attr) = getimagesize($filename);
    switch($image_type)
    {
      case 1:
        $p = imagecreatefromgif($filename);
        break;
      case 2:
        $p = imagecreatefromjpeg($filename);
        break;
      case 3:
        $p = imagecreatefrompng($filename);
        break;
    }

    switch($options['scale'])
    {
      case 'width':
        $destination_width = $this->width;
        $ratio = $this->width / $image_width;
        $destination_height = $image_height * $ratio;
        break;
      case 'height':
        $destination_height = $this->height;
        $ratio = $this->height / $image_height;
        $destination_width = $image_height * $ratio;
        break;
      case 'best':
        $vertical_space = $this->height - $image_height;
        $horizontal_space = $this->width - $image_width;
        if($vertical_space >= $horizontal_space)
        {
          $ratio =  $this->height / $image_height;
          $destination_height = $this->height;
          $destination_width = $image_width * $ratio;
        }
        else
        {
          $ratio = $this->width / $image_width; 
          $destination_width = $this->width;
          $destination_height = $image_height * $ratio;
        }
        break;
      default: //none
        $destination_width = $image_width;
        $destination_height = $image_height;
    }

    switch($options['x'])
    {
      case 'center':
      case 'centre':
        $destination_left = ($this->width - $destination_width) / 2;
        break;
      case 'left':
        $destination_left = 0;
        break;
      case 'right':
        $destination_left =  $this->width - $destination_width;
        break;
      default:
        $destination_left = ((int) $options['x'] > 0) ? (int) $options['x'] : 0;
    }

    switch($options['y'])
    {
      case 'middle':
        $destination_top = ($this->height - $destination_height) / 2;
        break;
      case 'top':
        $destination_top = 0;
        break;
      case 'bottom':
        $destination_top = $this->height - $destination_height;
        break;
      default:
        $destination_top = ((int) $options['y'] > 0) ? (int) $options['y'] : 0;
    }

    imagecopyresized($this->i,
                     $p,
                     $destination_left, /* dst_x */
                     $destination_top, /* dst_y */
                     0, /* src_x */
                     0, /* src_y */
                     $destination_width,
                     $destination_height,
                     $image_width,
                     $image_height);
  }
  private function insert_text($a, $b)
  {

  }
  private function insert_shape($shape)
  {
		/* 
		 * shape
		 * colour
		 * transparency
		 * x
		 * y
		 * w
		 * h
		 */
        $shape['color'] = (isset($shape['color'])) ? $shape['color'] : '#ffffff';
        $shape['x'] = ((int) $shape['x'] > 0) ? (int) $shape['x'] : 0;
        $shape['y'] = ((int) $shape['y'] > 0) ? (int) $shape['y'] : 0;
        $shape['w'] = ((int) $shape['w'] > 0) ? (int) $shape['w'] : 0;
        $shape['h'] = ((int) $shape['h'] > 0) ? (int) $shape['h'] : 0;
        $shape_colors = $this->convert_hex_to_rgb($shape['color']);
        $shape_color = imagecolorallocatealpha($this->i, $shape_colors['red'], $shape_colors['green'], $shape_colors['blue'], $shape['transparency']);

    if($shape['value'] == 'circle')
    {
      $shape['start'] = 0;
      $shape['end'] = 359;
    }
    switch($shape['value'])
    {
      case 'square':
      case 'rectangle':
        imagefilledrectangle($this->i, $shape['x'], $shape['y'], ($shape['x'] + $shape['w']), ($shape['y'] + $shape['h']), $shape_color);
        break;
      case 'arc':
          $shape['start'] = ((int) $shape['start'] > 0) ? $shape['start'] : 0;
          $shape['end'] = ((int) $shape['end'] > 0) ? $shape['end'] : 360;
          if($shape['outline'])
            imagefilledarc($this->i, $shape['x'], $shape['y'], $shape['w'], $shape['h'], $shape['start'], $shape['end'], $shape_color, IMG_ARC_PIE|IMG_ARC_NOFILL|IMG_ARC_EDGED);
          if($shape['fill'])
            imagefilledarc($this->i, $shape['x'], $shape['y'], $shape['w'], $shape['h'], $shape['start'], $shape['end'], $shape_color, IMG_ARC_PIE);
          else
            imagefilledarc($this->i, $shape['x'], $shape['y'], $shape['w'], $shape['h'], $shape['start'], $shape['end'], $shape_color, IMG_ARC_PIE|IMG_ARC_NOFILL);

        break;
      case 'line':
        imageline($this->i, $shape['x'], $shape['y'], $shape['w'], $shape['h'], $shape_color);
        break;
      case 'polygon':
        $shape['points'] = (isset($shape['points']) && is_array($shape['points'])) ? $shape['points'] : array(0);
        imagefilledpolygon($this->i, $shape['points'], count($shape['points']) / 2, $shape_color);

        break;
      case 'circle':
      case 'ellipse':
        imagefilledellipse($this->i, $shape['x'], $shape['y'], $shape['w'], $shape['h'], $shape_color);
        break;

    }
  }
	private function convert_hex_to_rgb($hex)
	{
		if (substr($hex,0,1) == "#")
    	$hex = substr($hex,1);

		if(strlen($hex) == 3)
		{
			$hex = $hex.$hex;
		}
		$R = hexdec(substr($hex,0,2));
		$G = hexdec(substr($hex,2,2));
		$B = hexdec(substr($hex,4,2));

		return array('red' => $R, 'green' => $G, 'blue' => $B);
	}
}
