<?php
require_once 'canvas.class.php';

$canvas = new \Acoustep\Canvas();

$canvas->width(500)
  ->height(700)
  ->background('#ff0000')
       ->add('image', 'test.jpg', array('scale' => 'best',
         'x' => 'centre',
         'y' => 'middle'))
       ->add('image', 'test2.jpg', array('scale' => 'width',
                                         'x' => 'centre',
                                         'y' => 'bottom'))
       ->output('test_best_overlay')
       ->type('jpg')
       ->create();
