# Express In Music Backend Test - Prayer Time Generation Application

This application generates prayer time voice-overs for subscribers based on their assigned prayer zones.

## Features

- Generates prayer times for each subscriber's box based on the assigned prayer zone.
- Plays a voice-over at the corresponding prayer times for each prayer zone.
- Sends error notifications via email in case of any failures during prayer time generation.

## Requirements
- PHP 7.4 or higher
- MySQL database
- Composer (dependency manager)
- PHPMailer library (for sending emails)
- mpg123 command-line utility (for playing MP3 files)

## Setup Instructions
1. Clone the repository:

    ```bash
    git clone https://github.com/chesspamungkas/eim-backend-test.git

2. Navigate to the project directory:
    cd eim-backend-test

3. Install the dependencies:

    ```bash
    composer install

4. Create a new MySQL database for the project.
5. Import the database schema using the `migrations.php` script:

    ```bash
    php database/migrations.php

6. Configure the application by creating a `.env` file based on the provided `.env.example` file. Update the database connection details and email configuration in the `.env` file.

7. Make sure the `voice/Time To Pray.mp3` file exists in the specified path.

8. Set up a cron job to run the `cron.php` script daily at the desired time.

## Usage
- The application will automatically generate prayer times for the next 7 days based on the subscriber and box settings.
- The `index.php` file can be run to manually generate prayer times and test the functionality.
- The `cron.php` script should be executed daily via a cron job to generate prayer times and play voice-overs.

## Chosen Libraries and Their Benefits
- **PHPMailer**: PHPMailer is a popular library for sending emails in PHP. It provides an easy-to-use interface for configuring SMTP settings and sending emails. It supports various email protocols and offers features like HTML email composition, attachments, and error handling. Using PHPMailer simplifies the process of sending error notifications via email in this application.
- **phpdotenv**: phpdotenv is a library that allows loading environment variables from a `.env` file into `$_ENV` superglobal variable. It provides a convenient way to store configuration variables separately from the codebase. By using phpdotenv, sensitive information like database credentials and email configuration can be kept secure and easily manageable.
- **PDO**: PDO (PHP Data Objects) is a built-in PHP extension that provides a consistent interface for accessing databases. It supports multiple database drivers and offers features like prepared statements, which help prevent SQL injection attacks. Using PDO ensures a secure and efficient way of interacting with the MySQL database in this application.

## Running Tests
To run the unit tests, execute the following command:

    ```bash
    vendor\bin\phpunit

## Future Improvements
- Implement a user-friendly web interface for managing subscribers, boxes, and prayer time settings.
- Integrate with additional prayer time APIs for more accurate and diverse prayer time data.
- Add support for multiple languages and regions to cater to a wider audience.
- Enhance the error handling and logging mechanisms for better debugging and monitoring.
- Optimize the application's performance and resource usage for scalability.


