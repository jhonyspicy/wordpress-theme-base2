<?php

require __DIR__ . '/vendor/autoload.php';

\Jhonyspicy\Wordpress\Theme\Base\Base::initialize(); //テーマベース
\Atomita\Wordpress\BreadcrumbNavigationFacade::expandFunction(); //パンくず

Theme::add_hooks();
Thumbnail::add_hooks();