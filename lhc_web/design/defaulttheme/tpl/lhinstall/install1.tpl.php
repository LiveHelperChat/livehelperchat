<img src="<?php echo erLhcoreClassDesign::design('images/general/logo.png');?>" alt="Live Helper Chat" title="Live Helper Chat" />

<h1>Installation</h1>

<form action="<?php echo erLhcoreClassDesign::baseurl('install/install')?>/1" method="POST">

<div class="panel">
  <p>You will need to grant write permissions on any of the red-marked folders. You can do this by changing its username to your web server's username or by changing permissions with a CHMOD 777 on the displayed files/folders.</p>
</div>

<h2>Checking folders permission</h2>

<table class="table table-sm">
    <tr>
        <td>I can write to &quot;cache/cacheconfig&quot; directory</td>
        <td><?php echo is_writable("cache/cacheconfig") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;cache/translations&quot; directory</td>
        <td><?php echo is_writable("cache/translations") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;cache/userinfo&quot; directory</td>
        <td><?php echo is_writable("cache/userinfo") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;cache/compiledtemplates&quot; directory</td>
        <td><?php echo is_writable("cache/compiledtemplates") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;settings/&quot; directory</td>
        <td><?php echo is_writable("settings/") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;var/storage&quot; directory</td>
        <td><?php echo is_writable("var/storage") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;var/userphoto&quot; directory</td>
        <td><?php echo is_writable("var/userphoto") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;var/storageform&quot; directory</td>
        <td><?php echo is_writable("var/storageform") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;var/storageadmintheme&quot; directory</td>
        <td><?php echo is_writable("var/storageadmintheme") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;var/botphoto&quot; directory</td>
        <td><?php echo is_writable("var/botphoto") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;var/bottrphoto&quot; directory</td>
        <td><?php echo is_writable("var/bottrphoto") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;var/storageinvitation&quot; directory</td>
        <td><?php echo is_writable("var/storageinvitation") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;var/storagedocshare&quot; directory</td>
        <td><?php echo is_writable("var/storagedocshare") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;var/storagetheme&quot; directory</td>
        <td><?php echo is_writable("var/storagetheme") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;var/tmpfiles&quot; directory</td>
        <td><?php echo is_writable("var/tmpfiles") ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>Is the php_curl extension installed</td>
        <td><?php echo extension_loaded ('curl' ) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'; ?></td>
    </tr>
    <tr>
        <td>Is the mbstring extension installed</td>
        <td><?php echo extension_loaded ('mbstring' ) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'; ?></td>
    </tr>
    <tr>
        <td>Is the php-pdo extension installed</td>
        <td><?php echo extension_loaded ('pdo_mysql' ) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'; ?></td>
    </tr>
    <tr>
        <td>Is the gd extension installed</td>
        <td><?php echo extension_loaded ('gd' ) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'; ?></td>
    </tr>
    <tr>
        <td>Is the json extension detected</td>
        <td><?php echo function_exists('json_encode') ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'; ?></td>
    </tr>
    <tr>
        <td>Is the bcmath extension detected</td>
        <td><?php echo extension_loaded('bcmath') ? '<span class="badge badge-success">Yes</span>' : '<span class="label label-warning">No, GEO detection will be disabled</span>'; ?></td>
    </tr>        
    <tr>
        <td>Minimum 5.4 PHP</td>
        <td><?php echo (version_compare(PHP_VERSION, '5.4.0','<')) ? '<span class="badge badge-danger">No</span>' : '<span class="badge badge-success">Yes</span>'; ?></td>
    </tr>
</table>
<br>

<input type="submit" class="btn btn-secondary" value="Next" name="Install">
<br /><br />

</form>