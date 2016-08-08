<html>
<body>
<?php
include_once("clsMatrix.php");
include_once("clsHelp.php");

$origin = array(
    array(1, 1, 0, 0, 0, 0, 0, 0, 0, 0),
    array(1, 1, 0, 1, 1, 0, 0, 0, 0, 0),
    array(0, 0, 0, 1, 1, 0, 0, 0, 0, 0),
    array(0, 0, 0, 0, 0, 1, 1, 1, 0, 0),
    array(1, 1, 1, 1, 1, 0, 0, 0, 0, 0),
    array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
    array(1, 1, 1, 0, 1, 0, 1, 1, 1, 1),
    array(1, 0, 0, 0, 1, 0, 1, 1, 1, 1),
    array(1, 0, 0, 0, 1, 0, 1, 1, 1, 1),
    array(1, 1, 0, 1, 1, 0, 0, 0, 0, 1)
);

    $objHW =new clsMatrix();
    $objHW->copyData($origin);
    $objHW->process();
    $objHW->drawResult();  
?>
</body>
</html>
