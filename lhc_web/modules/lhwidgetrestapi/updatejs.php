<html>
<head>
    <script src="//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('js/widgetv2/index.js')?>?t=<?php echo time()?>"></script>
    <meta http-equiv="cache-control" content="no-cache, must-revalidate, post-check=0, pre-check=0" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma" content="no-cache" />
</head>
<body>
<script>
    if (location.hash === '') {
        location.hash = "check";
        location.reload(true);
        console.log('reloading');
    }
</script>
</body>
</html>
<?php exit();?>