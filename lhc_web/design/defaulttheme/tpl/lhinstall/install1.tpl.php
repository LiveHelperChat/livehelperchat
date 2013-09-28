<img src="<?php echo erLhcoreClassDesign::design('images/general/logo.png');?>" alt="Live Helper Chat" title="Live Helper Chat" />

<h1>Installation</h1>

<form action="<?php echo erLhcoreClassDesign::baseurl('install/install')?>/1" method="POST">

<div class="panel">
  <p>You will need to grant write permissions on any of the red-marked folders. You can do this by changing its username to your web server's username or by changing permissions with a CHMOD 777 on the displayed files/folders.</p>
</div>

<h2>Checking folders permission</h2>

<table>
    <tr>
        <td>I can write to &quot;cache/cacheconfig/settings.ini.php&quot; file</td>
        <td><?php echo is_writable("cache/cacheconfig/settings.ini.php") ? '<span class="success label round">Yes</span>' : '<span class="round label alert">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;cache/translations&quot; directory</td>
        <td><?php echo is_writable("cache/translations") ? '<span class="success label round">Yes</span>' : '<span class="round label alert">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;cache/userinfo&quot; directory</td>
        <td><?php echo is_writable("cache/userinfo") ? '<span class="success label round">Yes</span>' : '<span class="round label alert">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;cache/compiledtemplates&quot; directory</td>
        <td><?php echo is_writable("cache/compiledtemplates") ? '<span class="success label round">Yes</span>' : '<span class="round label alert">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;settings/settings.ini.php&quot; directory</td>
        <td><?php echo is_writable("settings/settings.ini.php") ? '<span class="success label round">Yes</span>' : '<span class="round label alert">No</span>'?></td>
    </tr>
    <tr>
        <td>Is the php-pdo extension installed</td>
        <td><?php echo extension_loaded ('pdo_mysql' ) ? '<span class="success label round">Yes</span>' : '<span class="round label alert">No</span>'; ?></td>
    </tr>
</table>
<br>

<input type="submit" class="small button" value="Next" name="Install">
<br /><br />

</form>