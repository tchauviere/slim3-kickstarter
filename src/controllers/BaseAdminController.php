<?php
/**
 * Created by PhpStorm.
 * User: Thibaud
 * Date: 15/04/2019
 * Time: 12:54
 */

namespace Controllers;

use Psr\Container\ContainerInterface;

class BaseAdminController extends BaseController
{
    public function __construct(ContainerInterface $container) {
        parent::__construct($container);
        $this->checkIsAdmin();
    }

    public function checkIsAdmin() {
        $user = $this->getLoggedUser();

        if ($user->role->name !== 'admin') {
            header('Location: /');
            exit;
        }
    }
}
