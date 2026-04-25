<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    // Allows a user to add the block to their own Dashboard
    'block/logincounter:myaddinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'user' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/my:manageblocks'
    ),

    // Allows users with editing rights to add the block to courses/pages
    'block/logincounter:addinstance' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),

    // Allows admins and managers to view the time logs of any user
    'block/logincounter:viewalltimes' => array(
        'captype' => 'read',
        'riskbitmask' => RISK_DATALOSS | RISK_PERSONAL,
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW
        )
    ),
);