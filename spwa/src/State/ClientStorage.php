<?php

namespace Spwa\State;

enum ClientStorage: string
{
    case LocalStorage = 'localStorage';
    case SessionStorage = 'sessionStorage';
}
