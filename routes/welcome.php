<?php
$name = trim($_POST['name']);

if (empty($name)) {
    $this->set_rack('error', 'You must enter in your name.');
    $this->redirect('/');
}

$this->factory->search('SELECT id, name, email, jabber_id FROM users ORDER BY name');
$this->smarty->assign( 'guestName', $name);
$this->smarty->assign( 'employees', $this->factory->entries());
$this->smarty->display('welcome.tpl');