<?php

namespace BrickPHP\State;

enum ClientStorage: string
{
    case LocalStorage = 'localStorage';
    case SessionStorage = 'sessionStorage';
}
