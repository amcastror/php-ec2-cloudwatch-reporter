# PHP EC2 CloudWatch Reporter

A minimal PHP project to run in Docker, gather disk free space from host, fetch EC2 instance metadata (such as instance name), and report it to AWS CloudWatch metrics.

## Features
- Gathers disk free space from host
- Fetches EC2 instance metadata (e.g., instance name)
- Reports metrics to AWS CloudWatch (coming soon)

## Getting Started

### Prerequisites
- Docker
- AWS credentials (for CloudWatch access)

### Environment Variables
- `AWS_ACCESS_KEY_ID` - AWS access key for CloudWatch API
- `AWS_SECRET_ACCESS_KEY` - AWS secret access key for CloudWatch API
- `AWS_REGION` - AWS region (default: `us-east-1`)
- `SLEEP_DURATION` - Time to sleep in seconds after reporting (default: `43200` = 12 hours)

### Usage
1. Build the Docker image:
   ```sh
   docker build -t php-ec2-cloudwatch-reporter .
   ```
2. Run the container:
   ```sh
   docker run --rm -it -e AWS_ACCESS_KEY_ID -e AWS_SECRET_ACCESS_KEY php-ec2-cloudwatch-reporter
   ```

   To customize the sleep duration (e.g., 60 seconds for testing):
   ```sh
   docker run --rm -it -e AWS_ACCESS_KEY_ID -e AWS_SECRET_ACCESS_KEY -e SLEEP_DURATION=60 php-ec2-cloudwatch-reporter
   ```

### Project Structure
- `src/main.php` - Entry point PHP script
- `composer.json` - Composer dependencies
- `Dockerfile` - Container configuration

## Todo
- Implement CloudWatch metric reporting
- Tests and CI config
