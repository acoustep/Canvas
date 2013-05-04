<?php
require 'canvas.class.php';

$canvas = new \Acoustep\Canvas();

$canvas->width(500)
       ->height(500)
       ->add('image', 'test.jpg', array('scale' => 'best'))
       ->add('shape', 'square', array('color' => '#ff0000',
                            'x' => 0,
                            'y' => 0,
                            'w' => 250,
                            'h' => 250,
                            'transparency' => 40))
       ->add('image', 'test2.jpg', array('scale' => 'width',
                                         'x' => 'right',
                                         'y' => 'bottom'))
       ->add('shape', 'square', array('color' => '#00ff00',
                                      'x' => 150,
                                      'y' => 150,
                                      'w' => 100,
                                      'h' => 100,
                                      'transparency' => 90))
       ->output('test3')
       ->create();
