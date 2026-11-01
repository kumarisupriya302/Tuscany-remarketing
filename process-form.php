<?php
$lsq_enabled = true;

@include $_SERVER['DOCUMENT_ROOT'] . '/remote_tracker.php';

// ── Collect form fields from enquire.html ────────────────────────────────────
$fname = trim($_POST['firstname'] ?? $_POST['first_name'] ?? $_POST['first-name'] ?? '');
$lname = trim($_POST['lastname'] ?? $_POST['last_name'] ?? $_POST['last-name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['PhoneFormatLsq'] ?? $_POST['setMobile1'] ?? $_POST['phoneNumber'] ?? $_POST['phone'] ?? '');
$PhoneFormatLsq = $phone;
$dob = trim($_POST['dob'] ?? '');
$maritalStatus = trim($_POST['maritalStatus'] ?? $_POST['marital_status'] ?? $_POST['marital-status'] ?? '');
$passport = trim($_POST['passport'] ?? ''); // nationality select
$nationality = trim($_POST['nationality'] ?? $passport);
$county = trim($_POST['county'] ?? $_POST['state'] ?? '');
$town = trim($_POST['town'] ?? $_POST['city'] ?? '');
$travelmonth = trim($_POST['travelMonth'] ?? $_POST['travel-month'] ?? $_POST['travel_month'] ?? '');
$package = trim($_POST['package_select'] ?? 'Tuscany 4-Night Romantic Break');
$partner_name = trim($_POST['partner-name'] ?? $_POST['partner_name'] ?? '');
$partner_dob = trim($_POST['partner-dob'] ?? $_POST['partner_dob'] ?? '');
$partner_nationality = trim($_POST['partner-nationality'] ?? $_POST['partner_nationality'] ?? '');
$how_did_you_hear = trim($_POST['how_did_you_hear'] ?? '');
$preferred_check_in_1 = $travelmonth;
$preferred_check_in_2 = trim($_POST['check-in-month-2'] ?? '');
$preferred_check_in_3 = trim($_POST['check-in-month-3'] ?? '');

$consent_marketing = isset($_POST['marketing_consent']) && (string) $_POST['marketing_consent'] === '1';
$consent_eligibility = isset($_POST['accept-terms']) && (string) $_POST['accept-terms'] === '1';

if (!$consent_marketing || !$consent_eligibility) {
    http_response_code(400);
    echo 'Please accept both consent checkboxes before submitting the form.';
    exit;
}

if ($fname === '' || $lname === '' || $email === '' || $phone === '' || $dob === '' || $maritalStatus === '' || $nationality === '' || $county === '' || $town === '' || $travelmonth === '') {
    http_response_code(400);
    echo 'Please complete all required fields before submitting the form.';
    exit;
}

$accessKey = 'u$r6e6e6a278d5021554cff7c2fa6380787';
$secretKey = '447cfb38ef665b4d6f204031e1be3c0fa98a18be';
$api_url_base = 'https://api-in21.leadsquared.com/v2/LeadManagement.svc';

$campaign_folder = 'Tuscany-remarketing';
$campaign_url = 'https://karmaexperience.co.uk/Tuscany-remarketing';

$year_of_birth = '';
if ($dob !== '') {
    $dob_getyear = explode('-', $dob);
    $year_of_birth = $dob_getyear[0] ?? '';
}

// Normalize $_POST keys for campaign tracker + LSQ payload
$_POST['first-name'] = $fname;
$_POST['last-name'] = $lname;
$_POST['firstname'] = $fname;
$_POST['lastname'] = $lname;
$_POST['email'] = $email;
$_POST['phone'] = $phone;
$_POST['phoneNumber'] = $phone;
$_POST['setMobile1'] = $_POST['setMobile1'] ?? $phone;
$_POST['PhoneFormatLsq'] = $phone;
$_POST['dob'] = $dob;
$_POST['maritalStatus'] = $maritalStatus;
$_POST['marital_status'] = $maritalStatus;
$_POST['passport'] = $passport !== '' ? $passport : $nationality;
$_POST['nationality'] = $nationality;
$_POST['county'] = $county;
$_POST['state'] = $county;
$_POST['town'] = $town;
$_POST['city'] = $town;
$_POST['travel-month'] = $travelmonth;
$_POST['travelMonth'] = $travelmonth;
$_POST['package_select'] = $package;
$_POST['marketing_consent'] = $consent_marketing ? '1' : '0';
$_POST['accept-terms'] = $consent_eligibility ? '1' : '0';
$_POST['consent_marketing'] = $consent_marketing ? '1' : '0';
$_POST['consent_eligibility'] = $consent_eligibility ? '1' : '0';

$lead_brand = 'Timeshare Marketing';
$lead_sub_brand = 'Karma Experience UK';
$lead_location = 'General';
$lead_region = 'UK';
$lead_source = 'Facebook ads';
$lead_source_description = 'UK | FB LP | Tuscany Remarketing';
$Website = 'https://karmaexperience.co.uk/Tuscany-remarketing';
$resort_code = 'Colleoli';

if ($dob !== '') {
    $fdate = date('Y-m-d', strtotime($dob));
    $from = new DateTime($fdate);
    $to = new DateTime('today');
    $lead_age = $from->diff($to)->y;
} else {
    $lead_age = '';
}

$FirstName = ucfirst(strtolower($fname));
$LastName = ucfirst(strtolower($lname));

$data_string = json_encode([
    ['Attribute' => 'FirstName', 'Value' => $FirstName],
    ['Attribute' => 'LastName', 'Value' => $LastName],
    ['Attribute' => 'EmailAddress', 'Value' => $email],
    ['Attribute' => 'Phone', 'Value' => $phone],
    ['Attribute' => 'mx_date_of_birth', 'Value' => $dob],
    ['Attribute' => 'mx_Resort_Code', 'Value' => $resort_code],
    ['Attribute' => 'mx_Marital_status', 'Value' => $maritalStatus],
    ['Attribute' => 'mx_Spouse_Name', 'Value' => $partner_name],
    ['Attribute' => 'mx_Spouse_DOB', 'Value' => $partner_dob],
    ['Attribute' => 'mx_City', 'Value' => $town],
    ['Attribute' => 'mx_Packages', 'Value' => $package],
    ['Attribute' => 'mx_State', 'Value' => $county],
    ['Attribute' => 'mx_Nationality', 'Value' => $nationality],
    ['Attribute' => 'mx_Partner_Nationality', 'Value' => $partner_nationality],
    ['Attribute' => 'mx_How_did_you_hear_about_us', 'Value' => $how_did_you_hear],
    ['Attribute' => 'mx_Lead_Brand', 'Value' => $lead_brand],
    ['Attribute' => 'mx_Lead_Sub_Brand', 'Value' => $lead_sub_brand],
    ['Attribute' => 'mx_Lead_location', 'Value' => $lead_location],
    ['Attribute' => 'mx_Lead_region', 'Value' => $lead_region],
    ['Attribute' => 'Source', 'Value' => $lead_source],
    ['Attribute' => 'mx_Latest_Lead_Source', 'Value' => $lead_source],
    ['Attribute' => 'Website', 'Value' => $Website],
    ['Attribute' => 'mx_Passport', 'Value' => ($passport !== '' ? $passport : $nationality)],
    ['Attribute' => 'mx_Lead_source_description', 'Value' => $lead_source_description],
    ['Attribute' => 'mx_Preferred_Travel_Month', 'Value' => $preferred_check_in_1],
    ['Attribute' => 'Preferred CheckIN month 2', 'Value' => $preferred_check_in_2],
    ['Attribute' => 'mx_Preferred_Check.in_3', 'Value' => $preferred_check_in_3],
    ['Attribute' => 'mx_Year_of_Birth', 'Value' => $year_of_birth],
    ['Attribute' => 'mx_Age', 'Value' => (string) $lead_age],
    ['Attribute' => 'mx_Marketing_Consent', 'Value' => $consent_marketing ? 'Yes' : 'No'],
    ['Attribute' => 'mx_Eligibility_Consent', 'Value' => $consent_eligibility ? 'Yes' : 'No'],
], JSON_UNESCAPED_UNICODE);

if ($data_string === false) {
    $data_string = '[]';
}

// ── Log enquiry to campaigns tracker ─────────────────────────────────────────
$lsq_attributes = json_decode($data_string, true) ?? [];
$campaign_enquiry_id = null;
if (isset($tracker) && $tracker) {
    $campaign_enquiry_id = $tracker->logEnquiry($campaign_folder, $campaign_url, $_POST, $lsq_attributes);
}

function search_lead_by_phone($phone, $accessKey, $secretKey, $api_url_base)
{
    global $lsq_enabled;
    if (!($lsq_enabled ?? true)) {
        return [];
    }

    $url = $api_url_base . '/RetrieveLeadByPhoneNumber?accessKey=' . $accessKey . '&secretKey=' . $secretKey . '&phone=' . urlencode($phone);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true);
}

function search_lead_by_email($email, $accessKey, $secretKey, $api_url_base)
{
    global $lsq_enabled;
    if (!($lsq_enabled ?? true)) {
        return [];
    }

    $url = $api_url_base . '/Leads.GetByEmailaddress?accessKey=' . $accessKey . '&secretKey=' . $secretKey . '&emailaddress=' . urlencode($email);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true);
}

function create_lead($data, $accessKey, $secretKey, $api_url_base)
{
    global $lsq_enabled;
    if (!($lsq_enabled ?? true)) {
        return ['Status' => 'Success', 'Message' => 'LSQ globally disabled (mocked)'];
    }

    $url = $api_url_base . '/Lead.Capture?accessKey=' . $accessKey . '&secretKey=' . $secretKey;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data),
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true);
}

// Searching for leads by phone first — only capture to LSQ when age is 35+
$eligible_for_lsq = is_numeric($lead_age) && (int) $lead_age >= 35;

if (!$eligible_for_lsq) {
    header('Location: thank-you.html');
    exit;
}

$lead_data = search_lead_by_phone($PhoneFormatLsq, $accessKey, $secretKey, $api_url_base);

if (!empty($lead_data)) {
    $data_string_array = json_decode($data_string, true);
    $data_string_array[] = ['Attribute' => 'SearchBy', 'Value' => 'Phone'];
    $data_string = json_encode($data_string_array, JSON_UNESCAPED_UNICODE);
} else {
    $lead_data = search_lead_by_email($email, $accessKey, $secretKey, $api_url_base);

    if (!empty($lead_data)) {
        $data_string_array = json_decode($data_string, true);
        $data_string_array[] = ['Attribute' => 'SearchBy', 'Value' => 'EmailAddress'];
        $data_string = json_encode($data_string_array, JSON_UNESCAPED_UNICODE);
    }
}

$redirect_to = 'thank-you.html';
$lsq_action = 'create_lead';
$result = create_lead($data_string, $accessKey, $secretKey, $api_url_base);
if (($result['Status'] ?? '') !== 'Success') {
    error_log('LeadSquared Error [Tuscany-remarketing]: ' . json_encode($result));
}

// ── Log LSQ API result to campaign tracker ───────────────────────────────────
if (isset($tracker, $campaign_enquiry_id) && $tracker) {
    $tracker->logApiCall(
        $campaign_enquiry_id,
        'leadsquared',
        $lsq_action,
        json_decode($data_string, true) ?? [],
        $result,
        ($result['Status'] ?? '') !== 'Success'
            ? ($result['ExceptionMessage'] ?? $result['Message'] ?? 'LSQ error')
            : null
    );
}

if (!empty($redirect_to)) {
    header('Location: ' . $redirect_to);
    exit;
}
