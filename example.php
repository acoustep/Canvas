<?php
require_once 'canvas.class.php';

$canvas = new \Acoustep\Canvas();

$canvas->width(500)
       ->height(500)
       ->add('image', 'test.jpg', array('scale' => 'height', 
                                        'x' => 20))
       ->add('image', 'test2.jpg', array('scale' => 'width'))
       ->output('test_output')
       ->type('jpg')
       ->create();
