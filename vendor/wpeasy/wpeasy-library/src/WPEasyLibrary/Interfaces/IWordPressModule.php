<?php


namespace WPEasyLibrary\Interfaces;


interface IWordPressModule
{
    static function init();
    static function getDashboardView();
    static function getDescription();
    static function activate();
    static function deactivate();
    static function upgrade();
}