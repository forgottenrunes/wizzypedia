# WizzyPedia

### Installation

1. **Install XAMPP**
    - Download and install the latest version of [XAMPP](https://www.apachefriends.org/index.html).

2. **Copy Files**
    - Copy all project files to the `xampp/htdocs` directory.

3. **Create Database**
    - Open phpMyAdmin (http://localhost/phpmyadmin).
    - Create a new database named `wizzypedia`.
    - Import the database file provided with the project.

4. **Create Custom Domain**
    - Open your hosts file (located at `C:\Windows\System32\drivers\etc\hosts` on Windows or `/etc/hosts` on macOS/Linux).
    - Add the following line to create a custom domain:
      ```
      127.0.0.1 wizzypedia.local
      ```

5. **Run the Project**
    - Open your web browser and go to `http://wizzypedia.local`.

## Note

If you encounter any missing PHP extensions, you may need to uncomment the necessary extensions in the `xampp/php/php.ini` file.

To uncomment an extension, locate the extension in the `php.ini` file and remove the semicolon (`;`) at the beginning of the line.