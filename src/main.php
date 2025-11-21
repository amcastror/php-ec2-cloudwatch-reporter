<?php
// Entry point for reporter

require __DIR__ . '/../vendor/autoload.php';

// Gather disk free space
$diskFree = disk_free_space("/");

// Fetch EC2 instance metadata (instance ID & name)
// Metadata endpoint available in EC2 only
$instanceId = @file_get_contents('http://169.254.169.254/latest/meta-data/instance-id');
$instanceName = null;
if ($instanceId !== false) {
    $url = 'http://169.254.169.254/latest/meta-data/tags/instance/Name';
    $instanceName = @file_get_contents($url);
}

echo "Disk free: $diskFree bytes\n";
echo "EC2 Instance ID: " . ($instanceId ?: 'N/A') . "\n";
echo "EC2 Instance Name: " . ($instanceName ? $instanceName : 'N/A') . "\n";

// Placeholder for reporting to CloudWatch
// See https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/sending-cloudwatch-metrics.html
