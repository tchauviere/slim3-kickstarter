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

class BaseFrontController extends BaseController
{
    public function __construct(ContainerInterface $container) {
        parent::__construct($container);
    }

}
