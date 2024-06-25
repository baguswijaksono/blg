<h1 align=center>Blog</h1>

<p align=center>
    An extremely clean UI, simple-to-use, and minimalist personal website for blogging. 
</p>

![Alt text](https://baguswinaksono.my.id/assets/img/personal.png)

## Installation

### Local
To set up the project locally, follow these steps:

1. Clone the repository :
    ```sh
    git clone https://github.com/baguswijaksono/blog.git
    cd blog
    ```
2. Edit the dbconfig configuration on line:

    ```php
    public $servername = "localhost";
    public $username = "root";
    public $password = ".";
    public $dbname = "portfolio";
    ```
3. After updating the database configuration, initiate the database import 
4. Go to [https://phppasswordhash.com/](https://onlinephp.io/password-hash)
5. Generate a hashed password using PASSWORD_DEFAULT and place it in the variable $hashed_password within the getHashedPassword function.
   on dbconfig.php 
   ```php
    public function getHashedPassword()
    {
        $hashed_password = 'yourhashedpasswordresulthere';
        return $hashed_password;
    }
   ```
6. Start the server :
   To initiate the server, execute the following command in your terminal:
    ```sh
    php -S localhost:8000
    ```
    > This command will start a PHP server locally on port 8000. You can now access your web application by navigating to http://localhost:8000 in your web browser.

## Managing the Web App

Access the management interface for your web application at: http://localhost:8000/manage
