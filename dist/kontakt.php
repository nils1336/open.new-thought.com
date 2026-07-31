<?php
/**
 * kontakt.php
 * Nimmt das Erstgespräch-Formular der Startseite an und schickt die Anfrage
 * per E-Mail an Nils. Liegt in site/public/ und wird von Vite nach dist/ kopiert.
 * Antwortet immer JSON: {"ok":true} oder {"error":"..."}.
 */

header('Content-Type: application/json; charset=utf-8');

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array('error' => 'method_not_allowed'));
    exit;
}

// ── Honeypot: echte Nutzer füllen dieses Feld nie aus ─────────
if (isset($_POST['website']) && trim($_POST['website']) !== '') {
    echo json_encode(array('ok' => true)); // Bots ohne Hinweis abweisen
    exit;
}

// ── Eingaben ──────────────────────────────────────────────────
$name    = isset($_POST['name'])          ? trim($_POST['name'])          : '';
$email   = isset($_POST['email'])         ? trim($_POST['email'])         : '';
$company = isset($_POST['unternehmen'])   ? trim($_POST['unternehmen'])   : '';
$size    = isset($_POST['mitarbeitende']) ? trim($_POST['mitarbeitende']) : '';

$sizeLabels = array(
    '1-19'    => '1–19 Mitarbeitende',
    '20-49'   => '20–49 Mitarbeitende',
    '50-249'  => '50–249 Mitarbeitende',
    '250-999' => '250–999 Mitarbeitende',
    '1000+'   => '1.000+ Mitarbeitende',
);

$errors = array();
if ($name === '')                                { $errors[] = 'name'; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL))  { $errors[] = 'email'; }
if ($company === '')                             { $errors[] = 'unternehmen'; }
if (!isset($sizeLabels[$size]))                  { $errors[] = 'mitarbeitende'; }

if (count($errors) > 0) {
    http_response_code(422);
    echo json_encode(array('error' => 'validation', 'fields' => $errors));
    exit;
}

// Header-Injection über Name/Firma verhindern
$name    = str_replace(array("\r", "\n"), ' ', $name);
$company = str_replace(array("\r", "\n"), ' ', $company);

// Für die HTML-Mail escapen
$eName    = htmlspecialchars($name,               ENT_QUOTES, 'UTF-8');
$eEmail   = htmlspecialchars($email,              ENT_QUOTES, 'UTF-8');
$eCompany = htmlspecialchars($company,            ENT_QUOTES, 'UTF-8');
$eSize    = htmlspecialchars($sizeLabels[$size],  ENT_QUOTES, 'UTF-8');
$eWhen    = htmlspecialchars(date('d.m.Y H:i'),   ENT_QUOTES, 'UTF-8');
$eRef     = htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '—', ENT_QUOTES, 'UTF-8');

// ── Mail ──────────────────────────────────────────────────────
$subject = 'Erstgespräch-Anfrage: ' . $name . ' (' . $company . ')';

$td  = 'padding:9px 0;border-bottom:1px solid #f0f0ee;';
$tdL = $td . 'color:#6c6c66;width:38%;';

$html  = '<!doctype html><html><head><meta charset="UTF-8"></head>';
$html .= '<body style="font-family:-apple-system,Segoe UI,Helvetica,Arial,sans-serif;background:#f4f3ef;padding:24px;">';
$html .= '<div style="max-width:560px;margin:0 auto;background:#fff;border:1px solid #e7e6e1;border-radius:14px;padding:28px;">';
$html .= '<p style="margin:0 0 4px;font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:#2f6b43;font-weight:600;">Neue Anfrage über new-thought.com</p>';
$html .= '<h1 style="margin:0 0 22px;font-size:20px;color:#181816;">Erstgespräch angefragt</h1>';
$html .= '<table style="width:100%;border-collapse:collapse;font-size:14px;color:#181816;">';
$html .= '<tr><td style="' . $tdL . '">Name</td><td style="' . $td . 'font-weight:600;">' . $eName . '</td></tr>';
$html .= '<tr><td style="' . $tdL . '">E-Mail</td><td style="' . $td . '"><a href="mailto:' . $eEmail . '" style="color:#2f6b43;">' . $eEmail . '</a></td></tr>';
$html .= '<tr><td style="' . $tdL . '">Unternehmen</td><td style="' . $td . 'font-weight:600;">' . $eCompany . '</td></tr>';
$html .= '<tr><td style="' . $tdL . '">Größe</td><td style="' . $td . '">' . $eSize . '</td></tr>';
$html .= '<tr><td style="padding:9px 0;color:#6c6c66;">Eingegangen</td><td style="padding:9px 0;">' . $eWhen . '</td></tr>';
$html .= '</table>';
$html .= '<p style="margin:22px 0 0;font-size:13px;color:#6c6c66;line-height:1.5;">Zugesagt sind zwei bis drei Terminvorschläge innerhalb von 24 Stunden.<br>Einfach auf diese Mail antworten — die Antwort geht direkt an ' . $eEmail . '.</p>';
$html .= '<p style="margin:14px 0 0;font-size:11px;color:#9b9b95;">Quelle: ' . $eRef . '</p>';
$html .= '</div></body></html>';

$headers = implode("\r\n", array(
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: Erstgespraech new.thought <nils@new-thought.com>',
    'Reply-To: ' . $email,
));

$sent = mail('nils@new-thought.com', $subject, $html, $headers, '-f nils@new-thought.com');

if (!$sent) {
    http_response_code(500);
    echo json_encode(array('error' => 'send_failed'));
    exit;
}

echo json_encode(array('ok' => true));
