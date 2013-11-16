<?php namespace Ryun\Module;

abstract class AbstractModule
{
    public $name          = 'n/a';
    public $version       = 'n/a';
    public $author        = 'n/a';
    public $website       = 'n/a';
    public $license       = 'n/a';
    public $description   = 'n/a';
    public $admin_section = 'Content';
    public $autoload      = [];

    public function install() {return true;}
    public function upgrade() {return true;}

    public function admin_menu()
    {
        return [];
    }

    public function admin_quick_menu()
    {
        return [];
    }
}
