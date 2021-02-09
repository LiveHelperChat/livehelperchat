<?php
$tpl = erLhcoreClassTemplate::getInstance('lhchat/avatarbuilder.tpl.php');

$id = $Params['user_parameters']['id'];

$prefix = isset($_GET['prefix']) ? strip_tags($_GET['prefix']) : '';

if (empty($id)) {
    $id = erLhcoreClassModelForgotPassword::randomPassword();
} else {
    $idProps = explode('__', $id);
    $id = $idProps[0];

    $propsMapping = [
        'c' => 'clo',
        'h' => 'head',
        'm' => 'mouth',
        'e' => 'eyes',
        't' => 'top',
    ];

    $ver = null;

    foreach ($idProps as $prop) {
        $propParts = explode('_', $prop);

        if (count($propParts) == 3) {
            if (isset($propsMapping[$propParts[0]])) {
                $ver[$propsMapping[$propParts[0]]] = ['part' => $propParts[1], 'theme' => $propParts[2]];
            }
        }
    }

    $tpl->set('props',$ver);
}

$tpl->set('id',$id);
$tpl->set('prefix',$prefix);

echo $tpl->fetch();
exit();

?>