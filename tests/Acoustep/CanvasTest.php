<?php

class CanvasTest extends PHPUnit_Framework_TestCase
{
  protected $canvas;

  public function setUp()
  {
    $this->canvas = new \Acoustep\Canvas();
  }

  public function testInstance()
  {
    $this->assertInstanceOf('\Acoustep\Canvas', $this->canvas);
  }

  public function testDefaultParameters()
  {
    $this->assertEquals(0, $this->canvas->width); 
    $this->assertEquals(0, $this->canvas->height); 
    $this->assertEquals('#000000', $this->canvas->background); 
  }

  public function testChainSettingDimensions()
  {
    $this->canvas
         ->width(500)
         ->height(600)
         ->background('#ffffff');

    $this->assertEquals(500, $this->canvas->width);
    $this->assertEquals(600, $this->canvas->height);
    $this->assertEquals('#ffffff', $this->canvas->background);

    $this->canvas
         ->width(600)
         ->height(500)
         ->background('#ff0000');

    $this->assertEquals(600, $this->canvas->width);
    $this->assertEquals(500, $this->canvas->height);
    $this->assertEquals('#ff0000', $this->canvas->background);
  }
  public function testFileType()
  {
    $this->canvas->filetype('png');
    $this->assertEquals('png', $this->canvas->filetype);
    $this->canvas->filetype('jpg');
    $this->assertEquals('jpg', $this->canvas->filetype);
    $this->canvas->filetype('gif');
    $this->assertEquals('gif', $this->canvas->filetype);
    $this->canvas->filetype('jpeg');
    $this->assertEquals('jpeg', $this->canvas->filetype);
  }
}
