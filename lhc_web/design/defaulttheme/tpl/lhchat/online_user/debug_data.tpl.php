<hr />
<pre class="fs11"><?php 
$state = $online_user->getState();
if (isset($state['online_attr']) && !empty($state['online_attr'])) {
    $decoded = json_decode($state['online_attr'], true);
    if ($decoded !== null) {
        $state['online_attr'] = $decoded;
    }
}
if (isset($state['online_attr_system']) && !empty($state['online_attr_system'])) {
    $decoded = json_decode($state['online_attr_system'], true);
    if ($decoded !== null) {
        $state['online_attr_system'] = $decoded;
    }
}
echo htmlspecialchars(json_encode($state, JSON_PRETTY_PRINT)); 
?></pre>