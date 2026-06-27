<?php
header('Content-Type: text/plain');
$files = [
    'index.php',
    'models/User.php',
    'models/TarifSewa.php',
    'models/Transaksi.php',
    'controllers/UserController.php',
    'controllers/PelangganController.php',
    'controllers/TarifSewaController.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "$file: File does not exist\n";
        continue;
    }
    $output = [];
    $retval = 0;
    exec("C:\\xampp\\php\\php.exe -l " . escapeshellarg($file), $output, $retval);
    if ($retval === 0) {
        echo "$file: No syntax errors detected\n";
    } else {
        echo "$file: Syntax error! Details:\n" . implode("\n", $output) . "\n";
    }
}
unlink(__FILE__);
