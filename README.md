## Installation

### Local
To set up the project locally, follow these steps:

1. **Clone the Repository:**
    ```sh
    git clone https://github.com/baguswijaksono/blog.git
    cd blog
    ```

2. **Rename the Environment File:**
    ```sh
    mv .env.example .env
    ```

3. **Edit the Environment Configuration:**
    Open the `.env` file and update the database configuration:
    ```env
    DB_SERVERNAME=localhost
    DB_USERNAME=root
    DB_PASSWORD=
    DB_NAME=blog
    ```

4. **Initiate the Database Import:**
    Follow the instructions in your project for importing or setting up the database.

5. **Generate a Hashed Password:**
    Go to [https://phppasswordhash.com/](https://phppasswordhash.com/) to generate a hashed password using `PASSWORD_DEFAULT`. 

    Place the generated hashed password into the `$hashed_password` variable within the `getHashedPassword` function in `dbconfig.php`:
    ```php
    public function getHashedPassword()
    {
        $hashed_password = 'yourhashedpasswordresultshere';
        return $hashed_password;
    }
    ```

6. **Start the Server:**
    To start the PHP server, execute the following command in your terminal:
    ```sh
    php -S localhost:8000
    ```
    > This command will start a PHP server locally on port 8000. You can access your web application by navigating to [http://localhost:8000](http://localhost:8000) in your web browser.

## Managing the Web App

Access the management interface for your web application at: [http://localhost:8000/manage](http://localhost:8000/manage)
