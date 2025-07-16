<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;

class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();

        // Carrega o componente Authentication do plugin novo
        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Flash');
    }
}
