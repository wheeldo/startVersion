test

<?
$ds=DIRECTORY_SEPARATOR;

$file_location=dirname(__FILE__) . $ds . '..' . $ds .'vendor'. $ds .'PHPExcel'. $ds .'PHPExcel' . $ds .'IOFactory.php';
echo $file_location;
require_once $file_location;

echo "<hr>";
echo "ok";