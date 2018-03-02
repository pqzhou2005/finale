<?php
spl_autoload_register(function($className)
{
    require_once dirname(FINALE_ROOT) . '/' . str_replace('\\', '/', $className) . '.php';
});