<?php
/**
 * send-result.php
 * Sends email notification to Nils when someone submits their KI-Readiness results.
 */

// Capture PHP errors to log instead of output
@ini_set('log_errors', 1);
@ini_set('error_log', __DIR__ . '/ki-errors.log');
@error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$visitorEmail = filter_var($data['email']       ?? '', FILTER_SANITIZE_EMAIL);
$scores       = $data['scores']                 ?? [];
$overall      = round(floatval($data['overall'] ?? 0), 1);
$profileType  = htmlspecialchars($data['profileType'] ?? '', ENT_QUOTES, 'UTF-8');
$profileDesc  = htmlspecialchars($data['profileDesc'] ?? '', ENT_QUOTES, 'UTF-8');
$strongest    = $data['strongest']              ?? [];
$weakest      = $data['weakest']                ?? [];

if (!filter_var($visitorEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email']);
    exit;
}

// ── Scores table ─────────────────────────────────────────────
$scoresHtml = '';
foreach ($scores as $s) {
    $label = htmlspecialchars($s['label'] ?? '', ENT_QUOTES, 'UTF-8');
    $score = round(floatval($s['score'] ?? 0), 1);
    $pct   = round(($score / 10) * 100);
    $color = $score >= 7 ? '#22c55e' : ($score >= 4.5 ? '#f59e0b' : '#ff3eb5');
    $scoresHtml .= "
      <tr>
        <td style='padding:10px 0;font-size:13px;font-weight:600;color:#0e0e0c;border-bottom:1px solid #f0f0ee;white-space:nowrap;width:38%;'>{$label}</td>
        <td style='padding:10px 14px;border-bottom:1px solid #f0f0ee;'>
          <div style='background:#ece9e1;border-radius:99px;height:6px;overflow:hidden;'>
            <div style='background:{$color};height:100%;width:{$pct}%;border-radius:99px;'></div>
          </div>
        </td>
        <td style='padding:10px 0;font-size:13px;font-weight:700;color:{$color};text-align:right;white-space:nowrap;border-bottom:1px solid #f0f0ee;font-family:monospace;'>{$score}&thinsp;/&thinsp;10</td>
      </tr>";
}

// ── Stärken / Handlungsfelder ────────────────────────────────
$strongHtml = '';
foreach ($strongest as $d) {
    $n = htmlspecialchars($d['title'] ?? '', ENT_QUOTES, 'UTF-8');
    $s = round(floatval($d['score'] ?? 0), 1);
    $strongHtml .= "<div style='padding:7px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;margin-bottom:6px;display:flex;justify-content:space-between;font-size:13px;'>
      <span style='font-weight:600;color:#166534;'>{$n}</span>
      <span style='font-weight:700;color:#22c55e;font-family:monospace;'>{$s}</span></div>";
}
$weakHtml = '';
foreach ($weakest as $d) {
    $n = htmlspecialchars($d['title'] ?? '', ENT_QUOTES, 'UTF-8');
    $s = round(floatval($d['score'] ?? 0), 1);
    $weakHtml .= "<div style='padding:7px 12px;background:#fff0f9;border:1px solid #fbc8e7;border-radius:6px;margin-bottom:6px;display:flex;justify-content:space-between;font-size:13px;'>
      <span style='font-weight:600;color:#9d174d;'>{$n}</span>
      <span style='font-weight:700;color:#ff3eb5;font-family:monospace;'>{$s}</span></div>";
}

// ── Reply link ───────────────────────────────────────────────
$subjectEnc = rawurlencode('Ihr KI-Readiness-Ergebnis - Gespraech anbieten');
$bodyEnc    = rawurlencode("Hallo,\n\nIhr KI-Readiness-Score: {$overall}/10 ({$profileType}).\n\nIch wuerde gerne kurz ueber Ihre Ergebnisse sprechen - wann haben Sie 30 Minuten Zeit?\n\nBeste Gruesse\nNils");
$replyLink  = "mailto:{$visitorEmail}?subject={$subjectEnc}&body={$bodyEnc}";

$date = date('d.m.Y, H:i');

// ── HTML email body ──────────────────────────────────────────
$html = <<<HTML
<!DOCTYPE html>
<html lang="de">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;background:#f5f4f0;margin:0;padding:32px 16px;">
<div style="max-width:580px;margin:0 auto;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.09);">

  <div style="background:#0e0e0c;padding:28px 36px;">
    <div style="font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:8px;font-family:monospace;">KI-Readiness-Check &middot; new.thought &middot; {$date}</div>
    <div style="font-size:22px;font-weight:800;color:#fff;line-height:1.3;">Neues Ergebnis eingegangen</div>
  </div>

  <div style="padding:28px 36px 0;">

    <table style="width:100%;border-collapse:collapse;background:#f9f8f4;border-radius:10px;overflow:hidden;margin-bottom:28px;">
      <tr>
        <td style="padding:18px 20px;">
          <div style="font-size:11px;letter-spacing:.15em;text-transform:uppercase;color:#aaa;margin-bottom:5px;font-family:monospace;">E-Mail</div>
          <div style="font-size:16px;font-weight:700;color:#0e0e0c;">{$visitorEmail}</div>
        </td>
        <td style="padding:18px 20px;text-align:right;border-left:1px solid #ece9e1;">
          <div style="font-size:11px;letter-spacing:.15em;text-transform:uppercase;color:#aaa;margin-bottom:5px;font-family:monospace;">Gesamtscore</div>
          <div style="font-size:36px;font-weight:900;color:#ff3eb5;line-height:1;">{$overall}<span style="font-size:13px;font-weight:400;color:#aaa;"> /10</span></div>
        </td>
      </tr>
    </table>

    <div style="margin-bottom:26px;">
      <div style="font-size:11px;letter-spacing:.15em;text-transform:uppercase;color:#aaa;margin-bottom:10px;font-family:monospace;">KI-Profil</div>
      <div style="margin-bottom:10px;"><span style="display:inline-block;background:#ff3eb5;color:#fff;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:4px 12px;border-radius:4px;">{$profileType}</span></div>
      <div style="font-size:14px;line-height:1.65;color:#555;">{$profileDesc}</div>
    </div>

    <table style="width:100%;border-collapse:collapse;margin-bottom:26px;">
      <tr>
        <td style="width:50%;padding-right:12px;vertical-align:top;">
          <div style="font-size:11px;letter-spacing:.15em;text-transform:uppercase;color:#aaa;margin-bottom:8px;font-family:monospace;">Staerken</div>
          {$strongHtml}
        </td>
        <td style="width:50%;padding-left:12px;vertical-align:top;">
          <div style="font-size:11px;letter-spacing:.15em;text-transform:uppercase;color:#aaa;margin-bottom:8px;font-family:monospace;">Handlungsfelder</div>
          {$weakHtml}
        </td>
      </tr>
    </table>

    <div style="margin-bottom:26px;">
      <div style="font-size:11px;letter-spacing:.15em;text-transform:uppercase;color:#aaa;margin-bottom:12px;font-family:monospace;">Auswertung nach Dimension</div>
      <table style="width:100%;border-collapse:collapse;">{$scoresHtml}</table>
    </div>

    <div style="margin-bottom:36px;">
      <a href="{$replyLink}" style="display:inline-block;background:#ff3eb5;color:#fff;padding:14px 26px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:700;margin-right:12px;">Direkt antworten</a>
    </div>

  </div>

  <div style="padding:18px 36px;border-top:1px solid #ece9e1;">
    <p style="font-size:12px;color:#ccc;margin:0;">Automatisch von ki-readiness-check auf new-thought.com gesendet</p>
  </div>

</div>
</body>
</html>
HTML;

// ── Send email ───────────────────────────────────────────────
$notifyTo = 'nils@new-thought.com';

// Subject: UTF-8 encoded (required for umlauts/special chars in SMTP)
$mailSubject = '=?UTF-8?B?' . base64_encode('KI-Check: ' . $visitorEmail . ' | ' . $overall . '/10 | ' . $profileType) . '?=';

// From MUST be a real existing mailbox on Hostinger — noreply@ does not exist
$headers = implode("\r\n", [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: new.thought KI-Check <nils@new-thought.com>',
    'Reply-To: ' . $visitorEmail,
    'X-Mailer: PHP/' . phpversion()
]);

$sent = mail($notifyTo, $mailSubject, $html, $headers);

// ── Detailed log ─────────────────────────────────────────────
$status  = $sent ? 'SENT' : 'FAILED';
$logLine = date('Y-m-d H:i:s') . "\t{$status}\t{$visitorEmail}\t{$overall}/10\t{$profileType}\n";
@file_put_contents(__DIR__ . '/ki-leads.log', $logLine, FILE_APPEND | LOCK_EX);

echo json_encode([
    'success'            => $sent,
    'mail_available'     => function_exists('mail'),
    'logged'             => true
]);
