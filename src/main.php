<?php
// Entry point for reporter

require __DIR__ . '/../vendor/autoload.php';

use Aws\Ec2\Ec2Client;
use Aws\CloudWatch\CloudWatchClient;
use Aws\Exception\AwsException;

// Gather disk free space
$diskFree = disk_free_space("/") / 1024 / 1024 / 1024; // in GB

// Get the region from environment variable or default to us-east-1
$region = getenv('AWS_REGION') ?: 'us-east-1';

// Fetch EC2 instance metadata (instance ID & name)
// Metadata endpoint available in EC2 only
$instanceId = @file_get_contents('http://169.254.169.254/latest/meta-data/instance-id');
$instanceName = null;

if ($instanceId !== false) {
    
    try {
        // Create EC2 client (credentials provided by IAM instance role)
        $ec2Client = new Ec2Client([
            'version' => 'latest',
            'region' => $region
        ]);
        
        // Describe the instance to get tags
        $result = $ec2Client->describeInstances([
            'InstanceIds' => [$instanceId]
        ]);
        
        // Extract the Name tag from the instance
        $reservations = $result->get('Reservations');
        if (!empty($reservations)) {
            $instances = $reservations[0]['Instances'];
            if (!empty($instances)) {
                $tags = $instances[0]['Tags'] ?? [];
                foreach ($tags as $tag) {
                    if ($tag['Key'] === 'Name') {
                        $instanceName = $tag['Value'];
                        break;
                    }
                }
            }
        }
    } catch (AwsException $e) {
        // If AWS SDK fails, instance name remains null
        error_log("Failed to describe instance: " . $e->getMessage());
    }
}

echo "Disk free: " . $diskFree . " GB\n";
echo "EC2 Instance ID: " . ($instanceId ?: 'N/A') . "\n";
echo "EC2 Instance Name: " . ($instanceName ? $instanceName : 'N/A') . "\n";

// Report to CloudWatch metrics
if (!empty($instanceName) && $diskFree !== false) {
    try {
        // Create CloudWatch client
        $cloudWatchClient = new CloudWatchClient([
            'version' => 'latest',
            'region' => $region
        ]);
        
        // Send metric to CloudWatch
        $cloudWatchClient->putMetricData([
            'Namespace' => 'System/Linux',
            'MetricData' => [
                [
                    'MetricName' => 'DiskFreeSpace',
                    'Value' => $diskFree,
                    'Unit' => 'Gigabytes',
                    'Dimensions' => [
                        [
                            'Name' => 'InstanceName',
                            'Value' => $instanceName
                        ]
                    ]
                ]
            ]
        ]);
        
        echo "Metric sent to CloudWatch successfully\n";
    } catch (AwsException $e) {
        error_log("Failed to send metric to CloudWatch: " . $e->getMessage());
        echo "Failed to send metric to CloudWatch\n";
    }
} else {
    $reasons = [];
    if (empty($instanceName)) {
        $reasons[] = "Instance name not found";
    }
    if ($diskFree === false) {
        $reasons[] = "Unable to get disk free space";
    }
    echo implode(", ", $reasons) . ", skipping CloudWatch metric\n";
}
