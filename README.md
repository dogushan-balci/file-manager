# PHP File Manager

A secure and user-friendly file management system built with PHP. This application allows users to upload, download, and manage files through a web interface with customizable settings.

## Features

- 🔐 Secure authentication system
- 📤 File upload with size and type restrictions
- 📥 File download functionality
- 🗑️ File deletion
- 🔍 File search and sorting
- ⚙️ Customizable settings:
  - Allowed file extensions
  - Maximum file size
  - Username and password
- 📊 File statistics
- 🛡️ CSRF protection
- 📱 Responsive design with Bootstrap 5

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/php-file-manager.git
cd php-file-manager
```

2. Create a MySQL database and import the schema:
```bash
mysql -u your_username -p your_database < schema.sql
```

3. Configure the application:
   - Copy `config/config.example.php` to `config/config.php`
   - Update the following settings in `config/config.php`:
     ```php
     define('DB_HOST', 'your_database_host');
     define('DB_NAME', 'your_database_name');
     define('DB_USER', 'your_database_username');
     define('DB_PASS', 'your_database_password');
     define('APP_URL', 'http://your-domain.com');
     ```

4. Set proper permissions:
```bash
chmod 755 -R .
chmod 777 -R uploads/
```

5. Configure your web server:

For Apache, ensure you have the following in your virtual host configuration:
```apache
<Directory /path/to/php-file-manager>
    AllowOverride All
    Require all granted
</Directory>
```

For Nginx, add the following to your server block:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## Default Login

- Username: `admin`
- Password: `admin123`

**Important:** Change the default password after first login!

## Usage

1. Access the application through your web browser
2. Log in using your credentials
3. Use the interface to:
   - Upload files
   - Download files
   - Delete files
   - Search and sort files
   - Manage settings

## Settings

In the settings page, you can configure:

- Username and password
- Allowed file extensions (comma-separated)
- Maximum file size (in MB)

## Security Features

- Password hashing using MD5
- CSRF protection
- File type validation
- File size restrictions
- Secure file storage
- Session management

## Directory Structure

```
php-file-manager/
├── config/
│   ├── config.example.php
│   └── config.php
├── public/
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── settings.php
│   └── download.php
├── src/
│   ├── Database.php
│   ├── File.php
│   ├── Settings.php
│   └── Helper.php
├── uploads/
├── .htaccess
├── autoload.php
├── schema.sql
└── README.md
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

If you encounter any issues or have questions, please open an issue in the GitHub repository. 