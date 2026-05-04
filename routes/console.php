<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('about:clinic-saas', function (): void {
    $this->info('Clinica Vida+ pronta para instalacao.');
})->purpose('Mostra informacoes do projeto Clinica Vida+');
