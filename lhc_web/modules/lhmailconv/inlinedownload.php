<?php

try {
    $file = erLhcoreClassModelMailconvFile::fetch((int)$Params['user_parameters']['id']);

    if ($file->disposition != 'INLINE') {
        header('Content-Disposition: attachment; filename="'.$file->name.'"');
    }

    header('Content-type: '.$file->type);
    echo file_get_contents($file->file_path_server);

} catch (Exception $e) {
    header('Location: /');
    exit;
}
exit;

?>