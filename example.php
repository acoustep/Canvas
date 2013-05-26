<?php 
require_once 'vendor/autoload.php';

$canvas = new \Acoustep\Canvas();


$canvas->width(900)
       ->height(900)
       ->image('test.jpeg', array('scale' => 'width',
                                  'x' => 'left',
                                  'y' => 'bottom'))
       ->image('test.jpeg', array('scale' => 'width',
                                  'x' => 500,
                                  'y' => 500))
       ->output('test4')
       ->filetype('png')
       ->create();
