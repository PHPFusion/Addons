<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: form_sdk.php
| Author: Core Development Team
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once __DIR__.'/../maincore.php'; // You need to load this file first! Session will start
require_once FUSION_HEADER; // all scripts are loaded here.

// Start V9 Form
if (check_post('submit')) {
    
    // Method one - filter your self.
    $_unsafe_variables = [
        'id' => $_GET['id'],
        'input_1' => $_POST['input_1'],
        'input_2' => $_POST['input_2'],
    ];

    // Add your rules to filter these variables.
   //if (!is_numeric($_unsafe_variables['id'])) {
        // do something..
   //}
   // code all other sanitization checks .. have fun!

    // Method two - use PHP's built-in filter_var() function.
    $non_standard_safe = [
        'id' => get('id', FILTER_VALIDATE_INT),
        'input_1' => post('input'), // for string
        'input_2' => post(['input_2']),         // for multiple inputs
    ];

    $gold_standard_safe = [
        'id' => get('id', FILTER_VALIDATE_INT),
        // Sanitizer will determine three things:
        // 1) Filter according to the field type used and pair the sanitization rules are used automatically.
        // 2) Ensure required fields are not blank
        // 3) Ensure regex is matched if defined
        // Either 1,2 or 3 not pass, will result a const FUSION_NULL being declared by sanitizer
        'input_1' => sanitizer('input_1', '', 'input_1'),
        'input_2' => sanitizer(['input_2'], '', 'input_2'),
    ];

    // This function ensures system is safe and secure. It will return true if system is safe, otherwise false.
    if (fusion_safe()) {

        // if $Id is present, means your URL shows edit mode like 'http://www.example.com/index.php?id=123&action=edit' then we update entry else we insert a new entry
        // this function automates the process of inserting/updating a record in your database
        dbquery_insert(DB_PREFIX.'your_custom_db', $gold_standard_safe, $gold_standard_safe['id'] ? 'update' : 'save' );
        
        // see clean_request() function as well to assist with the proper path you will need.
        //$path = clean_request('success=true', ['action', 'id'], FALSE); // will return 'http://www.example.com/index.php?success=true' and remove action and id get request parameter.
        // This is REQUEST_URL
        redirect(FORM_REQUEST);
    
    } else {

        addnotice('danger', 'Invalid Input 1 or Invalid Input 2 or there are CSRF tokens being injected from remote site');
    }

   // Do not redirect here, as the fields will show all the validation labels that sanitizer() will send.
}

if (get('action') == 'edit' && check_get('id')) {
    $id = get('id', FILTER_VALIDATE_INT);

    $res = dbquery("SELECT FROM ".DB_PREFIX."your_custom_db WHERE id=:id", [':id'=>$id]);
    if (dbrows($res)) {
        $rows = dbarray($res);
    } else {
        addnotice('danger', 'Could not find your entry. Redirecting you out of this.');
        redirect(clean_request('', ['action', 'id'], FALSE));
    }
}

// Now the form in 7 lines for one textbox, one dropdown select, one checkbox, one textarea, and one submit button

echo openform('formName', 'POST').

form_text('input_1', 'Input 1', 'text default value goes here', ['required'=>TRUE]). // Set this field as required. 

form_select('input_2', 'Input 2', '', ['options' => [
    1 => 'No',
    2 => 'Yes'
]]).

form_textarea('input_3', 'Textarea', '', ['ext_tip'=>'(optional)']).

form_checkbox('input_4', 'Checkbox', ''),

form_button('submit', 'Submit Button', 'submit_value', ['class'=>'btn-primary']).

closeform();


require_once FUSION_FOOTER; // Cache everything above and send it to footer for output via ob_get_contents(), so you need to include this file.

?>
