<?php
require_once 'minim/plugins/tests/tests.php';
require_once 'minim/plugins/forms/new_forms.php';

function forms()
{
    return minim('new_forms');
}

class Minim_Forms_TestCase extends TestCase // {{{
{
    function test_construct_form() // {{
    {
        $form = forms()->create();
        $this->assertEqual('Minim_Form', get_class($form));
    } // }}}

    function test_add_form_field() // {{{
    {
        $form = forms()->create();
        $this->assertEqual(0, count($form->_fields));
        $form->text('title');
        $this->assertEqual(1, count($form->_fields),
            "Form should have one text field, found nothing");
        $this->assertEqual('text', $form->_fields['title']->type,
            "Form field 'title' should have type 'text'");
        $this->assertEqual('text', $form->title->type,
            "Form field accessor for 'title' should have type 'text'");
    } // }}}

    function test_form_render() // {{{
    {
        $form = forms()->create();
        $form->text('title');
        $out = $form->render();
        $this->assertTrue(strstr($out, '<form method='),
            "Form tag not found in form render output");
        $this->assertTrue(strstr($out, '<input type="text" name="title"'),
            "Form field 'title' not found in form render output");
    } // }}}

    function test_form_submit() // {{{
    {
        $form = forms()->create();
        $form->text('title');
        $GLOBALS['_POST'] = array(
            'title' => 'foo'
        );
        $this->assertTrue($form->was_submitted(),
            "Form data not found in \$_REQUEST");
        $this->assertEqual('foo', $form->title->value,
            "Submitted form field 'title' value mismatch: ".
            $form->title->value." != 'foo'");
        error_log(print_r($form, TRUE));
    } // }}}
} // }}}
