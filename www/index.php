<?php

require_once 'framework.php';
require_once 'view.php';
require_once 'home.php';
require_once 'js.php';

require_once 'ResolvedNode.php';
require_once 'TemplateNode.php';
require_once 'HtmlTemplateNode.php';
require_once 'ForTemplateNode.php';
require_once 'IfTemplateNode.php';
require_once 'TextTemplateNode.php';
require_once 'HtmlTokenizer.php';

session_start();

renderPage(new HomePage());