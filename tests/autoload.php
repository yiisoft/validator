<?php

declare(strict_types=1);

use Yiisoft\Validator\Tests\MockerExtension;

require_once dirname(__DIR__) . '/vendor/autoload.php';

defined('INTL_IDNA_VARIANT_UTS46') || define('INTL_IDNA_VARIANT_UTS46', 1);
MockerExtension::load();
