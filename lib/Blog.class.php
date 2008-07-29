<?php
breve()->register('BlogPost')
       ->table('post')
       ->field('id',      breve()->int(array('autoincrement' => TRUE)))
       ->field('title',   breve()->text(array('maxlength' => 100,
                                              'required' => TRUE,
                                              'not_null' => TRUE)))
       ->field('slug',    breve()->slug(array('from' => 'title',
                                              'read_only' => TRUE)))
       ->field('content', breve()->text(array('required' => TRUE,
                                              'not_null' => TRUE)))
       ->field('posted',  breve()->timestamp(array('auto_now' => TRUE)))
       ->field('author',  breve()->int(array('not_null' => TRUE)))
       ->field('tags',    breve()->text(array('maxlength' => 255)));

breve()->register('BlogComment')
       ->table('comment')
       ->field('id',      breve()->int(array('autoincrement' => TRUE)))
       ->field('post_id', breve()->int(array('not_null' => TRUE)))
       ->field('content', breve()->text(array('required' => TRUE,
                                              'not_null' => TRUE)))
       ->field('posted',  breve()->timestamp(array('auto_now' => TRUE)))
       ->field('author',  breve()->int(array('not_null' => TRUE)))
       ->field('email',   breve()->text(array('maxlength' => 255)));
