<?php
$this->factory->search("SELECT name, jabber_id, email FROM users WHERE id=" . (int) $_POST['employee']);
$entries  = $this->factory->entries();
$employee = $entries[0];

$message  = trim($_POST['message']);
$name     = trim($_POST['guestName']);

if (empty($name)) {
    $this->set_rack('error', 'You must enter in your name.');
    $this->redirect('/');
}

if (empty($message)) {
    $this->set_rack('error', 'You must enter in a message.');
    $this->redirect('/');
}

include dirname(__DIR__).'/lib/xmpp.php';
$bot = new XMPP($this->config['jabber_user'], $this->config['jabber_pass'], $this->config['jabber_host'], $this->config['jabber_port']);
$bot->special = array('employee' => $employee, 'name' => $name, 'message' => $message);

$bot->add('send_messages', 'send_messages');

function send_messages($xmpp) {
    $name     = htmlspecialchars($xmpp->special['name']);
    $message  = htmlspecialchars($xmpp->special['message']);
    $message  = "Hello {$xmpp->special['employee']['name']},\n\n{$name} is here to see you about {$message}!";

    if (isset($xmpp->online_users[$xmpp->special['employee']['jabber_id']])) {
        $instance = $xmpp->online_users[$xmpp->special['employee']['jabber_id']];

        foreach ($instance AS $employee) {
            $xmpp->send("<message to=\"{$employee}\" type='chat'><body>{$message}</body></message>");
        }

    } else {
        mail($xmpp->special['employee']['email'], "Someone is here to see you!", $message);
    }
}

$bot->start();

$this->smarty->assign('employeeName', $employee['name']);

$this->smarty->display('send.tpl');