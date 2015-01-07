<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        body {
            font: italic 18px Georgia, sans-serif;
            text-align: center;
            padding: 35px;
            color: #222;
        }
        #error {
            color: #e00;
            display: none;
        }
    </style>
</head>
<body>
    <div id="loading"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','Click blue eye at the top to request screen share')?>...</div>
    <div id="error"></div>
    <script type="text/javascript">
    window.onload = function() {
        setTimeout(function(){
        	parent.onIframeLoaded()
        },700);            
    }
    </script>
</body>
</html>