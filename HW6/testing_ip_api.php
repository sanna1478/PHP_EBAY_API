<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        <?php
            $zip_code = file_get_contents('http://ip-api.com/json/?fields=zip');
            $test = "atin";
         ?>
        <button type="button" onclick="getData()">Press</button>
        <script type="text/javascript">
            function getData(){
                var variable = <?php echo $zip_code;?>;
                console.log(variable);
            }
        </script>
    </body>
</html>
