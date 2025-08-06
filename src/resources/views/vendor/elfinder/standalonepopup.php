<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>elFinder 2.0</title>

    <!-- jQuery and jQuery UI (REQUIRED) -->
    <link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/themes/smoothness/jquery-ui.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

    <!-- elFinder CSS (REQUIRED) -->
    <link rel="stylesheet" type="text/css" href="<?= asset('src/'.$dir . '/css/elfinder.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('src/'.$dir . '/css/theme.css') ?>">

    <!-- elFinder JS (REQUIRED) -->
    <script src="<?= asset('src/'.$dir . '/js/elfinder.min.js') ?>"></script>

    <?php
    $locale = @$_GET['langCode'];
    if(!empty($locale)){ ?>
        <!-- elFinder translation (OPTIONAL) -->
        <script src="<?= asset('src/'.$dir . "/js/i18n/elfinder.$locale.js") ?>"></script>
    <?php } ?>
    <!-- Include jQuery, jQuery UI, elFinder (REQUIRED) -->

    <script type="text/javascript">
        $().ready(function () {
            var elf = $('#elfinder').elfinder({
                // set your elFinder options here
                <?php if(!empty($locale)){ ?>
                    lang: '<?= $locale ?>', // locale
                <?php } ?>
                customData: {
                    _token: '<?= csrf_token() ?>'
                },
                url: '<?= route("elfinder.connector") ?>',  // connector URL
                dialog: {width: 900, modal: true, title: 'Select a file'},
                resizable: false,
                commandsOptions: {
                    getfile: {
                        oncomplete: 'destroy'
                    }
                },
                getFileCallback: function (file) {
                    window.parent.processSelectedFile(file.path, '<?= $input_id?>');
                    parent.jQuery.colorbox.close();
                },
                //onlyMimes: ["image/png", "image/jpeg", "image/gif", "application/pdf"],
                onlyMimes: ["image/png", "image/jpeg", "image/gif", "application/pdf", 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/html', 'application/zip', 'application/x-rar', 'video/mp4', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                uiOptions : {
                    // toolbar configuration
                    toolbar : [
                        ['upload', 'info']
                    ]
                }
            }).elfinder('instance');
        });
    </script>


</head>
<body>
<!-- Element where elFinder will be created (REQUIRED) -->
<div id="elfinder"></div>

</body>
</html>
