<?php
$sHeaders  = 'MIME-Version: 1.0' . "\r\n";
$sHeaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$sHeaders .= 'From: noreply@noreply.com<noreply@noreply.com>' . "\r\n";
$sHeaders .= 'Reply-To: noreply@noreply.com' . "\r\n";
$sSubject = 'Your OTP for Application Login';
$sMessage  = <<<FORGOTYOURPWD
<html>
<body>
<p><strong>Dear {$sFirstName}</strong>,</p>
<p>Please use the following One-Time Passcode (OTP) to SignIn at the web address <a href="{$_SERVER[PHP_SELF]}" title="{$_SERVER[PHP_SELF]}">{$_SERVER[PHP_SELF]}</a><br />
<strong>{$sOTP}</strong>
<p><strong>Best Regards</strong></p>
<p><strong>Admin</strong></p>
</body>
</html>
FORGOTYOURPWD;
?>
