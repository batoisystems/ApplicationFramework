<?php
$sHeaders  = 'MIME-Version: 1.0' . "\r\n";
$sHeaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$sHeaders .= 'From: noreply@noreply.com<noreply@noreply.com>' . "\r\n";
$sHeaders .= 'Reply-To: noreply@noreply.com' . "\r\n";
$sSubject = 'Forgot your password?';
$sMessage  = <<<FORGOTYOURPWD
<html>
<body>
<p><strong>Dear {$sFirstName}</strong>,</p>
<p>Please use the following details to SignIn at our web address <a href="{$_SERVER[PHP_SELF]}" title="{$_SERVER[PHP_SELF]}">{$_SERVER[PHP_SELF]}</a><br />
<strong>Username:</strong> {$sUsername}<br />
<strong>Password:</strong> {$sPwd}</p>
<p><strong>Best Regards</strong></p>
<p><strong>Admin</strong></p>
</body>
</html>
FORGOTYOURPWD;
?>
