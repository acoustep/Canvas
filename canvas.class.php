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
	public function image($x)
	{
		$this->image = $x;
		return $this;
	}
	public function scale($x)
	{
		$this->scale = $x;
		return $this;
	}
  public function add(string $type, string $value, array $options)
  {
    switch($type)
    {
      case 'shape':

        break;
      case 'image':

        break;
      case 'text':

        break;
    }
  }
	public function add_shape($options=array())
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
		$this->shapes[] = $options;
		return $this;
	}
	/**
	 * Create image
	 * @return [type] [description]
	 */
	public function create()
	{
		$i = null;
		$i = ImageCreateTrueColor( $this->width, $this->height );
		$colour = $this->convert_hex_to_rgb($this->background);

			$background = imagecolorallocate( $i, $colour['red'] , $colour['green'], $colour['blue']);
		if($this->image)
		{
			list($image_width, $image_height, $image_type, $image_attr) = getimagesize($this->image);
			// echo 'image_width: '.$image_width;
			// echo 'image_height'.$image_height;
			// echo 'image type: '.$image_type;
			// echo 'image_attr: '.$image_attr;

			switch($image_type)
			{
				case 2: //IMAGETYPE_JPEG
					$p = imagecreatefromjpeg( $this->image );
					break;
				case 3: //IMAGETYPE_PNG
					$p = imagecreatefrompng( $this->image );
					break;
				case 1: //IMAGETYPE_GIF
					$p = imagecreatefromgif( $this->image );
					break;
			}

			if($this->scale)
				imagecopyresized( $i , /* destination image */
				                  $p , /* src image */
				                  0 , /* dst_x */
				                  0 , /* dst_y */
				                  0 , /* src_x */
				                  0 , /* src_y */
				                  $this->width , /* dst_w */
				                  $this->height , /* dst_h */
				                  $image_width , /* src_w */
				                  $image_height ); /* src_h */
			else
				imagecopyresized( $i , /* destination image */
				                  $p , /* src image */
				                  0 , /* dst_x */
				                  0 , /* dst_y */
				                  0 , /* src_x */
				                  0 , /* src_y */
				                  $image_width , /* dst_w */
				                  $image_height , /* dst_h */
				                  $image_width , /* src_w */
				                  $image_height ); /* src_h */
		}

		foreach($this->shapes as $shape)
		{
			$shape_colours = $this->convert_hex_to_rgb($shape['color']);
			// echo 'colour: '.$shape['color'];
			// var_dump($shape_colours);
			$shape_colour = imagecolorallocatealpha($i, $shape_colours['red'], $shape_colours['green'], $shape_colours['blue'], $shape['transparency']);
			
			imagefilledrectangle($i, $shape['x'], $shape['y'], ($shape['x'] + $shape['w']), ($shape['y'] + $shape['h']), $shape_colour);
		}

		switch($this->type)
		{
			case 'jpg':
			case 'jpeg':
				imagejpeg($i,$this->output.'.'.$this->type);
				break;
			case 'png':
				imagepng($i,$this->output.'.'.$this->type);
				break;
			case 'gif':
				imagegif($i,$this->output.'.'.$this->type);
				break;
		}

		imagedestroy($i);

		return $this;
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
