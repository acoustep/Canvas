<?php
require_once 'canvas.class.php';

$canvas = new \Acoustep\Canvas();

$canvas->width(500)
  ->height(500)
  ->background('#ff0000')
       ->add('image', 'test.jpg', array('scale' => false,
         'x' => 'right',
         'y' => 'bottom'))
       ->output('right_bottom')
       ->type('jpg')
       ->create();
