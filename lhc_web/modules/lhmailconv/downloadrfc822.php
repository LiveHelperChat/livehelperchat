<?php

$mail = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

header('Content-Disposition: attachment; filename="'.$mail->id.'.eml"');
header('Content-type: text/plain');

echo $mail->rfc822_body;

exit;
?>