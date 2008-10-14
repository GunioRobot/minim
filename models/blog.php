<?php
minim('orm')->register_model('BlogPost')
            ->table('post')
            ->default_sort('-posted')
            ->int('id', array('autoincrement' => TRUE,
                              'read_only' => TRUE))
            ->text('title', array('maxlength' => 100,
                                  'required' => TRUE,
                                  'not_null' => TRUE))
            ->slug('slug', array('from' => 'title',
                                 'maxlength' => 100,
                                 'not_null' => TRUE,
                                 'read_only' => TRUE))
            ->text('content', array('required' => TRUE,
                                    'not_null' => TRUE))
            ->timestamp('posted', array('auto_now' => TRUE,
                                        'not_null' => TRUE,
                                        'read_only' => TRUE))
            ->int('author', array('not_null' => TRUE,
                                  'read_only' => TRUE))
            ->text('tags', array('maxlength' => 255));

minim('orm')->register_model('BlogComment')
            ->table('comment')
            ->default_sort('-posted')
            ->int('id', array('autoincrement' => TRUE,
                              'read_only' => TRUE))
//            ->int('post_id', array('not_null' => TRUE))
            ->foreign_key('post', array('model' => 'BlogPost',
                                        'field' => 'id'))
            ->text('content', array('required' => TRUE,
                                    'not_null' => TRUE))
            ->timestamp('posted', array('auto_now' => TRUE))
            ->int('author', array('not_null' => TRUE));
