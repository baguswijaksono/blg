<?php

declare(strict_types=1);
$routes = [];
function loadEnv($filePath)
{
    if (!file_exists($filePath)) {
        return;
    }
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        list($key, $value) = array_map('trim', explode('=', $line, 2) + [NULL, NULL]);
        if ($key !== NULL) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}
function conn()
{
    static $conn = null;
    if ($conn === null) {
        loadEnv(__DIR__ . '/.env');
        $conn = new mysqli($_ENV['DB_HOST'] ?? '', $_ENV['DB_USERNAME'] ?? '', $_ENV['DB_PASSWORD'] ?? '', $_ENV['DB_NAME'] ?? '');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    }
    return $conn;
}
function editBlog()
{
    middleware();
    $result = conn()->query("SELECT id, topic FROM blogs");
    if (!$result || $result->num_rows === 0) {
        echo "No blogs available to edit.";
        return;
    }
    echo '<form action="/editBlogPage" method="POST">
            <label for="blog_id">Choose Blogs to edit</label><br>
            <select name="blog_id">';
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['id'] . "'>" . $row['topic'] . "</option>";
    }
    echo '</select><br>
          <input type="submit" name="delete" value="Go">
          </form>';
}
function incrementViews($content_id)
{
    $stmt = conn()->prepare("INSERT INTO views (content_id, views_count) VALUES (?, 1) ON DUPLICATE KEY UPDATE views_count = views_count + 1");
    $stmt->bind_param('i', $content_id);
    $stmt->execute();
}
function addRoute($route, $handler)
{
    global $routes;
    $routes[$route] = $handler;
}
function handleRequest($url)
{
    global $routes;
    foreach ($routes as $route => $handler) {
        if (preg_match("#^$route$#", $url, $matches)) {
            array_shift($matches);
            return call_user_func_array($handler, $matches);
        }
    }
    http_response_code(404);
    notFound();
}
function notFound()
{
    global $domain;
    head("404 Not Found", "Author Name", "Page not found", "404, not found", $domain);
    echo "<div class='pt-12 pb-4'>\n            <div class='bg-white min-h-full px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8'>\n                <div class='max-w-max mx-auto'>\n                    <main class='sm:flex'>\n                        <p class='text-4xl font-extrabold text-indigo-600 sm:text-5xl'>404</p>\n                        <div class='sm:ml-6'>\n                            <div class='sm:border-l sm:border-gray-200 sm:pl-6'>\n                                <h1 class='text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl'>Page not found</h1>\n                                <p class='mt-1 text-base text-gray-500'>Please check the URL in the address bar and try again.</p>\n                            </div>\n                            <div class='mt-10 flex space-x-3 sm:border-l sm:border-transparent sm:pl-6'>\n                                <a href='../'\n                                    class='inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'>Go back home</a>\n                            </div>\n                        </div>\n                    </main>\n                </div>\n            </div>\n        </div>\n    </body>\n    </html>";
}
function nav() {}
function foot()
{
    echo '<script>
            hljs.highlightAll();
            function toggleNavbar(collapseID) {
                document.getElementById(collapseID).classList.toggle("hidden");
                document.getElementById(collapseID).classList.toggle("block");
            }
            function openDropdown(event, dropdownID) {
                let element = event.target;
                while (element.nodeName !== "A") {
                    element = element.parentNode;
                }
                document.getElementById(dropdownID).classList.toggle("hidden");
                document.getElementById(dropdownID).classList.toggle("block");
            }
            document.addEventListener("DOMContentLoaded", function() {
                var gallery = document.getElementById("gallery");
                var viewer = new Viewer(gallery, {
                    navbar: true,
                    toolbar: true,
                    tooltip: true,
                    fullscreen: true,
                    viewed() {
                        viewer.zoomTo(1);
                    }
                });
            });
        </script>
    </body>
    </html>';
}
function blogList()
{
    global $name, $domain;
    head('Blog List', $name, "Blog List", "Blog List", $domain);
    echo '<div class="pt-12 pb-4">';
    nav();
    echo '<div class="px-4 pt-4 prose prose-indigo">
            <h1 class="text-center mt-6 mb-6">Blog</h1>
            <div>';
    $sql = "SELECT id, topic, shortdesc, created_at FROM blogs WHERE is_public = 1";
    $result = mysqli_query(conn(), $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="pt-3.5"><a class="!no-underline" href="/' . htmlspecialchars($row['topic'], ENT_QUOTES, 'UTF-8') . '">
                    <h3 class="text-sm text-gray-700">' . htmlspecialchars($row['topic'], ENT_QUOTES, 'UTF-8') . '
                        <div class="inline-flex items-center px-2.5 py-0.25 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            ' . getViews(conn(), $row['id']) . ' Views
                        </div>
                    </h3>
                </a>
                <p class="max-w-[40ch] text-xs text-gray-500">' . htmlspecialchars($row['shortdesc'], ENT_QUOTES, 'UTF-8') . '<br><br><small class="text-gray-400">' . (new DateTime($row['created_at']))->format('F j, Y') . '</small></p>
            </div>';
        }
    }
    echo '  </div>
        </div>
    </div>';
    foot();
}
function getBlogs($conn, $project)
{
    $stmt = mysqli_prepare($conn, "SELECT * FROM blogs WHERE topic = ?");
    mysqli_stmt_bind_param($stmt, "s", $project);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return $result;
}
function getTags($conn, $blog_id)
{
    $stmt = $conn->prepare("SELECT t.tag_name FROM tags t JOIN blog_tags bt ON t.id = bt.tag_id WHERE bt.blog_id = ?");
    $stmt->bind_param('i', $blog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}
function getViews($conn, $content_id)
{
    $stmt = $conn->prepare("SELECT views_count FROM views WHERE content_id = ?");
    $stmt->bind_param('i', $content_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['views_count'];
    } else {
        return 0;
    }
}
function blog($topic)
{
    $conn = conn();
    $result = getBlogs($conn, $topic);
    if (mysqli_num_rows($result) <= 0) {
        notFound();
        return;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        incrementViews($row["id"]);
        $getTagresult = getTags($conn, $row["id"]);
        $tags = [];
        if ($getTagresult->num_rows > 0) {
            while ($tag = $getTagresult->fetch_assoc()) {
                $tags[] = $tag['tag_name'];
            }
        }
        $tagsString = implode(', ', $tags);
        global $name, $domain;
        head('Blog', $name, $row['shortdesc'], $tagsString, $domain);
        echo "<div class='pt-12 pb-4'>";
        nav();
        echo "<div class='px-4 pt-4 prose prose-indigo'>\n                <h1 class='text-center mt-6 mb-6'>" . htmlspecialchars($row["title"], ENT_QUOTES, 'UTF-8') . "</h1>\n                <div class='text-center'>";
        foreach ($tags as $tag) {
            echo "<div class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800'>\n                    <a class='!no-underline' href='../tag/" . htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') . "</a>\n                  </div>";
        }
        echo "</div>" . $row["hypertext"] . "</div></div>";
    }
    foot();
}
function tagList()
{
    global $name, $domain;
    head('Tag List', $name, "Tag list for blog", "Tag List", $domain);
    echo "<div class='pt-12 pb-4'>";
    nav();
    echo "<div class='px-4 pt-4 prose prose-indigo'>\n            <h1 class='text-center mt-6 mb-6'>Tags</h1>";
    $result = getAllTags(conn());
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tagName = htmlspecialchars($row["tag_name"], ENT_QUOTES, 'UTF-8');
            echo "<div class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800'>
                    <a class='!no-underline' href='/tag/$tagName'>$tagName</a>
                  </div>";
        }
    }
    echo "  </div>\n        </div>";
    foot();
}
function addBlog()
{
    middleware();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $stmt = conn()->prepare("INSERT INTO blogs (topic, docname, title, shortdesc, hypertext, is_public) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $_POST['topic'], $_POST['docname'], $_POST['title'], $_POST['shortdesc'], $_POST['hypertext'], $_POST['is_public']);
        $stmt->execute();
        returnToHome();
    }
    display_form('blog', '/addBlog', 'add');
}
function display_form($type, $form_action, $operation, $row = null)
{
    $submitButtonText = ($operation == 'add') ? 'Submit' : 'Update';
    $typepost = ($type == 'blog') ? 'topic' : 'project';
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($typepost, ENT_QUOTES, 'UTF-8') . '</title>
        <script src="assets/js/tinymce.min.js"></script>
    </head>
    <body>
        <div>
            <form action="' . htmlspecialchars($form_action, ENT_QUOTES, 'UTF-8') . '" method="post">
                <label for="' . htmlspecialchars($typepost, ENT_QUOTES, 'UTF-8') . '">Topic:</label><br>
                <input type="text" id="' . htmlspecialchars($typepost, ENT_QUOTES, 'UTF-8') . '" name="' . htmlspecialchars($typepost, ENT_QUOTES, 'UTF-8') . '" value="' . ($row ? htmlspecialchars($row[$typepost], ENT_QUOTES, 'UTF-8') : '') . '"><br>
                <label for="docname">Docname:</label><br>
                <input type="text" id="docname" name="docname" value="' . ($row ? htmlspecialchars($row['docname'], ENT_QUOTES, 'UTF-8') : '') . '"><br>
                <label for="title">Title:</label><br>
                <input type="text" id="title" name="title" value="' . ($row ? htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') : '') . '"><br>
                <label for="hypertext">Hypertext:</label><br>
                <textarea id="hypertext" name="hypertext" rows="4" cols="50">' . ($row ? htmlspecialchars($row['hypertext'], ENT_QUOTES, 'UTF-8') : '') . '</textarea><br>
                <label for="shortdesc">Short Description:</label><br>
                <textarea id="shortdesc" name="shortdesc" rows="4" cols="50">' . ($row ? htmlspecialchars($row['shortdesc'], ENT_QUOTES, 'UTF-8') : '') . '</textarea><br>';
    if ($operation == 'edit') {
        echo '<input name="blog_id" type="hidden" value="' . $row['id'] . '">';
    }
    echo '<label for="is_public">Is Public:</label>
                <select id="is_public" name="is_public">
                    <option value="0"' . ($row && $row['is_public'] == 0 ? ' selected' : '') . '>0</option>
                    <option value="1"' . ($row && $row['is_public'] == 1 ? ' selected' : '') . '>1</option>
                </select><br>
                <input type="submit" value="' . htmlspecialchars($submitButtonText, ENT_QUOTES, 'UTF-8') . '">
            </form>
            <script>
                tinymce.init({
                    selector: "#hypertext",
                    height: 500,
                    plugins: "code",
                    toolbar: "undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | code",
                });
            </script>
        </div>
    </body>
    </html>';
}
function middleware()
{
    session_start();
    $hashed_password = '$2y$10$lvnXRVqbNt5UsyrW9awqe.PZFjupDx7EzlogSRa3gtJSVHAyUPlwS';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['original_password'])) {
        if (password_verify($_POST['password'], $hashed_password)) {
            $_SESSION['original_password'] = $_POST['password'];
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            echo 'Invalid password. Please try again.';
        }
    }
    if (!isset($_SESSION['original_password']) || !password_verify($_SESSION['original_password'], $hashed_password)) {
        echo '<form action="" method="post">
            <input type="password" name="password" id="password" placeholder="Password">
            <button type="submit">Unlock</button>
          </form>';
        exit;
    }
}
function addImage()
{
    middleware();
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $uploadDir = "assets/img/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid("image_") . "." . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
            returnToHome();
        }
    }
    echo '<form action="/addImage" method="post" enctype="multipart/form-data">
                <label for="image">Select Image:</label>
                <input type="file" name="image" id="image" accept="image/*"><br>
                <input type="submit" value="Upload">
              </form>';
}
function manage()
{
    middleware();
    echo '<a href="/">Home</a><br>
          <a href="/assets/img/">Access Image Storage</a><br>
          <a href="/addImage">Add Image</a><br>
          <a href="/deleteImage">Delete Image</a><br>
          <a href="/addBlog">Add Blog</a><br>
          <a href="/editBlog">Edit Blog</a><br>
          <a href="/deleteBlog">Delete Blog</a><br>';
}
function editBlogPage()
{
    middleware();
    $stmt = conn()->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $_POST['blog_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    display_form('blog', '/editBlogHandle', 'edit', $row);
}
function editBlogHandle()
{
    middleware();
    $stmt = conn()->prepare("UPDATE blogs SET topic = ?, docname = ?, title = ?, hypertext = ?, shortdesc = ?, is_public = ? WHERE id = ?");
    $stmt->bind_param("sssssii", $_POST['topic'], $_POST['docname'], $_POST['title'], $_POST['hypertext'], $_POST['shortdesc'], $_POST['is_public'], $_POST['blog_id']);
    $stmt->execute();
    returnToHome();
}
function deleteBlog()
{
    middleware();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        $stmt = conn()->prepare("CALL delete_blog(?)");
        $stmt->bind_param("i", $_POST['blog_id']);
        $stmt->execute();
        returnToHome();
        return;
    }
    $result = conn()->query("SELECT id, topic FROM blogs");
    if (!$result || $result->num_rows === 0) {
        echo "No blogs available to delete.";
        return;
    }
    echo '<form action="/deleteBlog" method="POST">
            <label for="blog_id">Choose Blogs to Delete</label><br>
            <select name="blog_id">';
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['id'] . "'>" . $row['topic'] . "</option>";
    }
    echo '</select><br>
          <input type="submit" name="delete" value="Go">
          </form>';
}
function deleteImage()
{
    middleware();
    $imageDirectory = 'assets/img/';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image'])) {
        $selectedImage = $_POST['image'];
        $imageFilePath = $imageDirectory . $selectedImage;
        if (file_exists($imageFilePath) && unlink($imageFilePath)) {
            returnToHome();
            return;
        }
    }
    $files = glob($imageDirectory . '*.{jpg,jpeg,png,gif}', GLOB_BRACE); ?>
    <form method="post" action="/deleteImage">
        <label for="image">Select Image to Delete:</label>
        <select name="image" id="image">
            <?php foreach ($files as $file) {
                $fileName = basename($file);
                echo "<option value=\"$fileName\">$fileName</option>";
            } ?>
        </select><br>
        <input type="submit" value="Delete">
    </form>
<?php }
function tag($tag)
{
    global $name, $domain;
    head('Tag Specify', $name, $tag, $tag, $domain);
    echo "<div class='pt-12 pb-4'>";
    nav();
    echo "<div class='px-4 pt-4 prose prose-indigo'>
                    <h1 class='text-center mt-6 mb-6'>{$tag} Tag</h1>";
    $stmt = conn()->prepare("SELECT b.topic, b.shortdesc, b.created_at FROM blogs b JOIN blog_tags bt ON b.id = bt.blog_id JOIN tags t ON bt.tag_id = t.id WHERE t.tag_name = ? AND b.is_public = 1 GROUP BY b.topic, b.shortdesc, b.created_at");
    $stmt->bind_param('s', $tag);
    $stmt->execute();
    $resultBlogs = $stmt->get_result();
    if ($resultBlogs) {
        while ($row = $resultBlogs->fetch_assoc()) {
            $formattedDate = (new DateTime($row['created_at']))->format('F j, Y');
            echo "<div class='pt-3.5'>
                            <a class='!no-underline' href='../blog/{$row['topic']}'>
                                <h3 class='text-sm text-gray-700'>{$row['topic']}
                                    <div class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800'>
                                        blog
                                    </div>
                                </h3>
                            </a>
                            <p class='max-w-[40ch] text-xs text-gray-500'>
                                {$row['shortdesc']}
                                <br><br>
                                <small class='text-gray-400'>{$formattedDate}</small>
                            </p>
                        </div>";
        }
    }
    echo "</div></div>";
    foot();
}
function returnToHome()
{
    header('Location: /');
    exit;
}
function getAllTags($conn)
{
    $result = $conn->query("SELECT tag_name FROM tags");
    return $result;
}
function migrate()
{
    middleware();
    $queries = ["CREATE TABLE IF NOT EXISTS `blogs` (`id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,`topic` varchar(255) NOT NULL,`docname` varchar(255) DEFAULT NULL,`title` longtext NOT NULL,`hypertext` longtext DEFAULT NULL,`shortdesc` varchar(255) DEFAULT NULL,`is_public` BOOLEAN DEFAULT NULL,`created_at` timestamp NOT NULL DEFAULT current_timestamp(),PRIMARY KEY (`id`));", "CREATE TABLE IF NOT EXISTS `blog_tags` (`blog_id` int(6) UNSIGNED NOT NULL,`tag_id` int(6) UNSIGNED NOT NULL,PRIMARY KEY (`blog_id`, `tag_id`),KEY `blog_id` (`blog_id`),KEY `tag_id` (`tag_id`));", "CREATE TABLE IF NOT EXISTS `tags` (`id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,`tag_name` varchar(100) NOT NULL,PRIMARY KEY (`id`));", "CREATE TABLE IF NOT EXISTS `views` (`id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,`content_id` int(6) UNSIGNED DEFAULT NULL,`views_count` int(6) UNSIGNED DEFAULT 0,PRIMARY KEY (`id`));"];
    foreach ($queries as $query) {
        if (conn()->query($query) !== TRUE) {
            echo "Error creating table: " . conn()->error;
            return;
        }
    }
    echo "Tables created successfully.";
}
function head($doc_title, $author, $description, $keyword, $domain)
{
    echo '<!DOCTYPE html>
    <html lang="en-US">
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta charset="utf-8">

        <meta property="og:type" content="website">
        <link rel="stylesheet" href="/assets/css/tailwind.min.css">
        <link rel="stylesheet" href="/assets/css/atom-one-dark.min.css">
        <script src="/assets/js/highlight.min.js"></script>
        <link rel="stylesheet" href="/assets/css/viewer.min.css">
        <script src="/assets/js/viewer.min.js"></script>
        <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
        <link rel="preconnect" href="https://rsms.me/">
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    </head>
    <body class="font-sans mx-auto max-w-prose">';
}
function listen()
{
    addRoute('/migrate', 'migrate');
    addRoute('/tag', 'tagList');
    addRoute('/addBlog', 'addBlog');
    addRoute('/addImage', 'addImage');
    addRoute('/manage', 'manage');
    addRoute('/editBlog', 'editBlog');
    addRoute('/editBlogPage', 'editBlogPage');
    addRoute('/editBlogHandle', 'editBlogHandle');
    addRoute('/deleteBlog', 'deleteBlog');
    addRoute('/deleteImage', 'deleteImage');
    addRoute('/tag/([\w-]+)', 'tag');
    addRoute('/', 'blogList');
    addRoute('/([\w-]+)', 'blog');
    handleRequest($_SERVER['REQUEST_URI']);
}
listen();
