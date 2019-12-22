<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    BaseAdminController.php
 * Date:    15/04/2019
 * Time:    12:54
 */

namespace Controllers\Core;

use Psr\Container\ContainerInterface;

class BaseAdminController extends BaseController
{
    public function __construct(ContainerInterface $container) {
        parent::__construct($container);
    }

    /**
     * Check if logged user (if one) is an Admin from its Role, if not, redirect to /
     */
    public function checkIsAdmin() {
        $user = $this->getLoggedUser();

        if ($user->role->name !== 'admin') {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
    }
}
