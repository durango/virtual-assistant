<?php
$name = trim($_POST['name']);

if (empty($name)) {
    $this->set_rack('error', 'You must enter in your name.');
    $this->redirect('/');
}

$this->smarty->assign('guestName', $name);
$this->smarty->display('welcome.tpl');