<?php

/*
This script is not PSR12 compliant in that it breaks "2.3. Side Effects".  Compliance would be impractcal in that the
code to generate the error message when failing to include a file containing function msg would have to replicate that
function's action and "$prefix = basename(__FILE__, ".php");" would have to be in this file, not in the function.  This
script is anyway trivial so the reasons for having "2.3. Side Effects" are not relevant.

In https://stackoverflow.com/questions/13298795/psr-1-2-3-side-effects-rule, one of the contributors to PSR-1 wrote
"don't adopt PSR-1 for the sake of. If you have a valid reason to not follow the rules in certain spots, break the
rules. They're not laws. Common sense reigns supreme"

The warning about "2.3. Side Effects" can be suppressed by using --exclude:
phpcs --standard=PSR12 --exclude=PSR1.Files.SideEffects
*/

function msg($class, $message)
{
    global $debug;

    $prefix = basename(__FILE__, ".php");
    $exit = false;

    switch ($class) {
        case 'I':
            $prefix .= ' ';
            break;
        case 'D':
            if (!$debug) {
                return 0;
            }
            $prefix .= ' Debug: ';
            break;
        case 'W':
            $prefix .= ' Warning: ';
            break;
        case 'E':
            $exit = true;
            $prefix .= ' Error: ';
            break;
        default:
            msg('E', "msg called with invalid class '$class' and \$message '$message'");
    }
    $message = "$prefix$message";
    error_log(print_r($message, true));
    if ($exit) {
        exit(1);
    }
    return 0;
}

// Get configuration variables
const CONF_PHP = "./conf.php";
if (! include CONF_PHP) {
    msg('E', "Failed to include " . CONF_PHP);
}
filter_var(to, FILTER_VALIDATE_EMAIL)
    or msg('E', "Invalid email recipient " . to);

// Read POST
$payload = file_get_contents('php://input')
    or msg('E', "file_get_contents('php://input') failed");

// Parse JSON
$decoded = json_decode($payload, true);
if ($decoded === null) {
    msg('E', "json_decode($payload, true) failed");
}
$string = print_r($decoded, true);
msg('D', "\$decoded: $string");

isset($decoded['from'])
    or msg('E', "\$decoded['from'] is not set");
$from = $decoded['from'];
//msg('D', "\$from: $from");

isset($decoded['receivedStamp'])
    or msg('E', "\$decoded['receivedStamp'] is not set");
$receivedStamp = $decoded['receivedStamp'];
//msg('D', "\$receivedStamp: $receivedStamp");

isset($decoded['sentStamp'])
    or msg('E', "\$decoded['sentStamp'] is not set");
$sentStamp = $decoded['sentStamp'];
//msg('D', "\$sentStamp: $sentStamp");

isset($decoded['text'])
    or msg('E', "\$decoded['text'] is not set");
$text = $decoded['text'];
//msg('D', "\$text: $text");

// No need to sanitise strings to be used in email subject and body because they will not be interpreted

// Send email
$receivedTime = date('D j M H:i', round($receivedStamp / 1000));
$subject = "SMS from $from $receivedTime";
$receivedTime = date('r', round($receivedStamp / 1000));
$sentTime = date('r', round($sentStamp / 1000));
$message = "Sent: $sentTime\nReceived: $receivedTime\n\n$text";
$headers = "From: apache.cs7@charlesmatkinson.org";
if (mail(to, $subject, $message, $headers)) {
    msg('I', "Email sent");
} else {
    msg('E', 'Email sending failed');
}

// Defend against flooding
sleep(sleep);
