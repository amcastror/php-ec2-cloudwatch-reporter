<?php
// Entry point for reporter

require __DIR__ . '/../vendor/autoload.php';

use Aws\Ec2\Ec2Client;

// Gather disk free space
$diskFree = disk_free_space("/");

// Fetch EC2 instance metadata (instance ID & name)
// Metadata endpoint available in EC2 only
$instanceId = @file_get_contents('http://169.254.169.254/latest/meta-data/instance-id');
$instanceName = null;

if ($instanceId !== false) {
    // Get the region from EC2 metadata
    $region = @file_get_contents('http://169.254.169.254/latest/meta-data/placement/region');
    
    if ($region !== false) {
        try {
            // Create EC2 client
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
        } catch (Exception $e) {
            // If AWS SDK fails, instance name remains null
            error_log("Failed to describe instance: " . $e->getMessage());
        }
    }
}

echo "Disk free: " . ($diskFree / 1024 / 1024 / 1024) . " GB\n";
echo "EC2 Instance ID: " . ($instanceId ?: 'N/A') . "\n";
echo "EC2 Instance Name: " . ($instanceName ? $instanceName : 'N/A') . "\n";

// Placeholder for reporting to CloudWatch
// See https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/sending-cloudwatch-metrics.html
