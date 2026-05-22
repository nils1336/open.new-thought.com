<?php
// DIAGNOSE-TOOL — nach dem Test löschen!
header('Content-Type: text/plain; charset=UTF-8');

echo "=== Hostinger Mail-Diagnose ===\n\n";
echo "PHP Version:      " . phpversion() . "\n";
echo "mail() verfügbar: " . (function_exists('mail') ? 'JA' : 'NEIN') . "\n\n";

// Test-Mail senden
$to      = 'nils@new-thought.com';
$subject = 'Mail-Test von new-thought.com';
$body    = 'Diese Mail wurde automatisch als Diagnose-Test gesendet.';
$headers = 'From: nils@new-thought.com' . "\r\n" .
           'Reply-To: nils@new-thought.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

$result = @mail($to, $subject, $body, $headers);

echo "mail() Rückgabe:  " . ($result ? 'TRUE (angenommen)' : 'FALSE (abgelehnt)') . "\n\n";

// Log-Datei prüfen
$logFile = __DIR__ . '/ki-leads.log';
echo "ki-leads.log:     " . (file_exists($logFile) ? "EXISTIERT\n" : "NICHT VORHANDEN\n");
if (file_exists($logFile)) {
    $lines = file($logFile);
    $count = count($lines);
    echo "Eintraege:        {$count}\n";
    echo "Letzter Eintrag:  " . trim(end($lines)) . "\n";
}
echo "\n";

// Fehlerdatei prüfen
$errFile = __DIR__ . '/ki-errors.log';
echo "ki-errors.log:    " . (file_exists($errFile) ? "EXISTIERT\n" : "NICHT VORHANDEN\n");
if (file_exists($errFile)) {
    echo "Inhalt:\n" . file_get_contents($errFile) . "\n";
}

echo "\n=== Ende Diagnose ===\n";
echo "Diese Datei (mail-test.php) bitte nach dem Test loeschen!\n";
