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

### Usage
1. Build the Docker image:
   ```sh
   docker build -t php-ec2-cloudwatch-reporter .
   ```
2. Run the container:
   ```sh
   docker run --rm -it -e AWS_ACCESS_KEY_ID -e AWS_SECRET_ACCESS_KEY php-ec2-cloudwatch-reporter
   ```

### Project Structure
- `src/main.php` - Entry point PHP script
- `composer.json` - Composer dependencies
- `Dockerfile` - Container configuration

## Todo
- Implement CloudWatch metric reporting
- Tests and CI config
