<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

function cyberpanel_extra_config() {
    return [
        'name' => 'CyberPanel Extra',
        'description' => 'Addon para gestionar tabla extra de credenciales SSH en CyberPanel.',
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
            // Si ya existe, nos aseguramos de que la columna ssh_port esté presente
            if (!Capsule::schema()->hasColumn('mod_cyberpanel_extra', 'ssh_port')) {
                Capsule::schema()->table('mod_cyberpanel_extra', function ($table) {
                    $table->integer('ssh_port')->default(22)->after('ssh_pass');
                });
            }
        }

        return [
            'status' => 'success',
            'description' => 'Tabla mod_cyberpanel_extra creada/actualizada exitosamente.',
        ];
    } catch (\Throwable $e) {
        return [
            'status' => 'error',
            'description' => 'Error al crear/actualizar la tabla: ' . $e->getMessage(),
        ];
    }
}

function cyberpanel_extra_deactivate() {
    try {
        // Si quieres eliminar la tabla al desactivar, descomenta esta línea:
        // Capsule::schema()->dropIfExists('mod_cyberpanel_extra');

        return [
            'status' => 'success',
            'description' => 'Addon desactivado correctamente.',
        ];
    } catch (\Throwable $e) {
        return [
            'status' => 'error',
            'description' => 'Error al desactivar: ' . $e->getMessage(),
        ];
    }
}

function cyberpanel_extra_output($vars) {
    echo "<p>CyberPanel Extra está activo. La tabla <code>mod_cyberpanel_extra</code> está disponible para el módulo de servidor.</p>";
}
