<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wallet = $_POST['wallet'] ?? '';

    if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $wallet)) {
        echo "Invalid wallet address.";
        exit;
    }

    // Get user IP address
    $ip = $_SERVER['REMOTE_ADDR'];
    $time = date("Y-m-d H:i:s");

    // Log to file
    $logLine = "$time | IP: $ip | Wallet: $wallet" . PHP_EOL;
    file_put_contents("log.txt", $logLine, FILE_APPEND | LOCK_EX);

    // GraphQL query
    $query = [
        'query' => 'query {
            zoraTokenAllocation(
                identifierWalletAddresses: ["' . $wallet . '"],
                zoraClaimContractEnv: PRODUCTION
            ) {
                totalTokensEarned {
                    totalTokens
                }
            }
        }'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.zora.co/universal/graphql");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($query));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    $tokens = $data['data']['zoraTokenAllocation']['totalTokensEarned']['totalTokens'] ?? null;

    if ($tokens !== null) {
        echo "Wallet <code>$wallet</code> has earned <strong>$tokens</strong> ZORA tokens.";
    } else {
        echo "No tokens found or API error.";
    }
}
?>
