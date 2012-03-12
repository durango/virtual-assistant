<?php
$this->factory->search("SELECT name, jabber_id, email FROM users WHERE id=" . (int) $_POST['employee']);
$entries  = $this->factory->entries();
$employee = $entries[0];

include dirname(__DIR__).'/lib/xmpp.php';
$bot = new XMPP($this->config['jabber_user'], $this->config['jabber_pass'], $this->config['jabber_host'], $this->config['jabber_port']);
$bot->start();

if (isset($bot->online_users[$employee['jabber_id']])) {
    $this->smarty->assign('jabber', true);
} else {
    $this->smarty->assign('jabber', false);
}

$name = trim($_POST['guestName']);

if (empty($name)) {
    $this->set_rack('error', 'You must enter in your name.');
    $this->redirect('/');
}

$this->smarty->assign( 'guestName', $name);
$this->smarty->assign( 'employee', (int) $_POST['employee']);
$this->smarty->assign( 'employeeName', $employee['name']);
$this->smarty->display('more.tpl');