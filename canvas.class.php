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

	/**
	 * Set options for the canvas
	 * @param array $options options to edit
	 */
	public function set(array $options)
	{
		if(!is_array($options))
			throw new Exception('Expects an array.');

		if(isset($options['width']))
		{
			$this->width($options['width']);
		}

		if(isset($options['height']))
		{
			$this->height($options['height']);
		}

		if(isset($options['output']))
		{
			$this->output($options['output']);
		}

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
	public function shape(string $filename, array $options)
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
          $this->insert_shape($layer['value'], $layer['options']);
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
        // TO DO
        break;
      default: //none
        $destination_width = $image_width;
        $destination_height = $image_height;
    }

    switch($options['x'])
    {
      case 'center':
      case 'centre':

        break;
      case 'left':

        break;
      case 'right':
        
        break;
      default:
        $x = ((int) $options['x'] > 0) ? (int) $options['x'] : 0;
    }
    $y = ((int) $options['y'] > 0) ? (int) $options['y'] : 0;

    imagecopyresized($this->i,
                     $p,
                     $x, /* dst_x */
                     $y, /* dst_y */
                     0, /* src_x */
                     0, /* src_y */
                     $destination_width,
                     $destination_height,
                     $image_width,
                     $image_height);
  }
  private function insert_text($a, $b)
  {
    // TO DO
  }
  private function insert_shape($a, $b)
  {

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
