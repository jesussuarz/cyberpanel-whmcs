<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

function cyberpanel_extra_config() {
    return [
        'name' => 'CyberPanel Extra',
        'description' => 'Addon to manage an extra table for SSH credentials in CyberPanel.',
        'author' => 'Soporte Server',
        'language' => 'english',
        'version' => '1.1',
    ];
}

function cyberpanel_extra_activate() {
    try {
        if (!Capsule::schema()->hasTable('mod_cyberpanel_extra')) {
            Capsule::schema()->create('mod_cyberpanel_extra', function ($table) {
                $table->increments('id');
                $table->integer('serviceid')->unsigned();
                $table->string('ssh_user', 255)->default('');
                $table->string('ssh_pass', 255)->default('');
                $table->integer('ssh_port')->default(22);
                $table->unique('serviceid');
            });
        } else {
            // If it already exists, ensure the ssh_port column is present
            if (!Capsule::schema()->hasColumn('mod_cyberpanel_extra', 'ssh_port')) {
                Capsule::schema()->table('mod_cyberpanel_extra', function ($table) {
                    $table->integer('ssh_port')->default(22)->after('ssh_pass');
                });
            }
        }

        return [
            'status' => 'success',
            'description' => 'Table mod_cyberpanel_extra created/updated successfully.',
        ];
    } catch (\Throwable $e) {
        return [
            'status' => 'error',
            'description' => 'Error creating/updating the table: ' . $e->getMessage(),
        ];
    }
}

function cyberpanel_extra_deactivate() {
    try {
        // If you want to drop the table on deactivation, uncomment this line:
        Capsule::schema()->dropIfExists('mod_cyberpanel_extra');

        return [
            'status' => 'success',
            'description' => 'Addon deactivated successfully.',
        ];
    } catch (\Throwable $e) {
        return [
            'status' => 'error',
            'description' => 'Error deactivating addon: ' . $e->getMessage(),
        ];
    }
}

function cyberpanel_extra_output($vars) {
    echo "<p>CyberPanel Extra is active. The table <code>mod_cyberpanel_extra</code> is available for the server module.</p>";
}
