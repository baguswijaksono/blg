<?php

declare(strict_types=1);

$domain = "https://baguswinaksono.ny.id";
$routes = [];
$url = $_SERVER['REQUEST_URI'];

$name = 'Bagus Winaksono';
$about = 'I am';
$linktreedata = [
    [
        'url' => 'https://www.github.com/baguswijaksono',
        'platform_name' => 'github'
    ],
    [
        'url' => 'https://www.linkedin.com/in/yourprofile',
        'platform_name' => 'linkedin'
    ],
    [
        'url' => 'https://www.youtube.com/channel/yourchannel',
        'platform_name' => 'youtube'
    ],
    [
        'url' => 'https://www.youtube.com/channel/yourchannel',
        'platform_name' => 'stack-overflow'
    ]
];

$conn = null;
$hashed_password = '$2y$10$v4Zr9MgjWSDa1vM1l6RHgOeX/joovOcp217fxNvhB5YdFu1Ukak5O';
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
        $conn = new mysqli(
            $_ENV['DB_HOST'] ?? '',
            $_ENV['DB_USERNAME'] ?? '',
            $_ENV['DB_PASSWORD'] ?? '',
            $_ENV['DB_NAME'] ?? ''
        );
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    }
    return $conn;
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
    echo "<div class='pt-12 pb-4'>
            <div class='bg-white min-h-full px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8'>
                <div class='max-w-max mx-auto'>
                    <main class='sm:flex'>
                        <p class='text-4xl font-extrabold text-indigo-600 sm:text-5xl'>404</p>
                        <div class='sm:ml-6'>
                            <div class='sm:border-l sm:border-gray-200 sm:pl-6'>
                                <h1 class='text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl'>Page not found</h1>
                                <p class='mt-1 text-base text-gray-500'>Please check the URL in the address bar and try again.</p>
                            </div>
                            <div class='mt-10 flex space-x-3 sm:border-l sm:border-transparent sm:pl-6'>
                                <a href='../'
                                    class='inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'>Go back home</a>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </body>
    </html>";
}

function home()
{
    global $domain, $name, $about, $linktreedata;
    head($name, $name, "Personal website of " . $name . ", showcasing blog posts, projects, and professional profiles.", $name . " personal website, blog, projects, professional profiles", $domain);

    echo "<div class='pt-12 pb-4'>";
    nav();
    echo "<div class='mx-auto text-center prose prose-indigo'>
            <div>
                <img class='object-cover mx-auto h-36 w-36 rounded-full' src='assets/img/profile.jpg' alt='Profile Picture'>
                <h1>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</h1>
            </div>
            <p class='text-gray-500 pb-4'>" . htmlspecialchars($about, ENT_QUOTES, 'UTF-8') . "</p>
          </div>
          </div>
          <section>
            <div class='flex flex-wrap text-center'>
                <div class='w-full pt-4'>
                    <div>";
    if (!empty($linktreedata)) {
        foreach ($linktreedata as $row) {
            echo "<a href='" . htmlspecialchars($row['url'], ENT_QUOTES, 'UTF-8') . "' target='_blank' rel='noopener noreferrer'>
                    <button class='bg-white text-lightBlue-600 font-normal h-10 w-10 items-center justify-center align-center rounded-full outline-none focus:outline-none mr-2' type='button'>
                        <i class='fab fa-" . htmlspecialchars($row['platform_name'], ENT_QUOTES, 'UTF-8') . "'></i>
                    </button>
                  </a>";
        }
    }
    echo "          </div>
                </div>
            </div>
          </section>";
    foot();
}

function nav()
{
    echo '<nav class="flex flex-wrap items-center justify-center px-2 mb-6">
            <div class="container px-4 mx-auto flex flex-wrap items-center justify-center">
                <div class="w-full relative flex justify-center">
                    <button type="button" onclick="toggleNavbar(\'navbar\')" class="rounded-md inline-flex items-center justify-center text-gray-500 md:hidden" aria-expanded="false">
                        <span class="sr-only">Open menu</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
                <div class="flex hidden md:block" id="navbar">
                    <div class="flex flex-col md:flex-row mr-auto w-full">';
    $menuItems = ['Home' => '/', 'Blog' => '/blog', 'Tag' => '/tag'];
    $currentEndpoint = rtrim($_SERVER['REQUEST_URI'], '/');
    foreach ($menuItems as $label => $url) {
        $isActive = ($currentEndpoint == rtrim($url, '/')) ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900';
        echo '<a class="text-sm ' . $isActive . ' px-3 py-2 lg:py-1 mx-auto uppercase ' . ($isActive == 'text-gray-900' ? 'active' : '') . '" href="' . $url . '">' . $label . '</a>';
    }
    echo '          </div>
                </div>
            </div>
        </nav>';
}

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
            echo '<div class="pt-3.5"><a class="!no-underline" href="/blog/' . htmlspecialchars($row['topic'], ENT_QUOTES, 'UTF-8') . '">
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

function incrementViews($conn, $content_id)
{
    $conn->prepare("INSERT INTO views (content_id, views_count) VALUES (?, 1) ON DUPLICATE KEY UPDATE views_count = views_count + 1")->bind_param('i', $content_id)->execute();
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
        incrementViews($conn, $row["id"]);
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
        echo "<div class='px-4 pt-4 prose prose-indigo'>
                <h1 class='text-center mt-6 mb-6'>" . htmlspecialchars($row["title"], ENT_QUOTES, 'UTF-8') . "</h1>
                <div class='text-center'>";
        foreach ($tags as $tag) {
            echo "<div class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800'>
                    <a class='!no-underline' href='../tag/" . htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') . "</a>
                  </div>";
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
    echo "<div class='px-4 pt-4 prose prose-indigo'>
            <h1 class='text-center mt-6 mb-6'>Tags</h1>";

    $result = getAllTags(conn());
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tagName = htmlspecialchars($row["tag_name"], ENT_QUOTES, 'UTF-8');
            echo "<div class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800'>
                    <a class='!no-underline' href='/tag/$tagName'>$tagName</a>
                  </div>";
        }
    }

    echo "  </div>
        </div>";

    foot();
}

function attachTag()
{
    middleware();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['tag_id'], $_POST['id_post'])) {
        echo '<form action="/attachTag" method="POST">
                    <label for="tag_id">Choose Tag to attach</label><br>
                    <select name="tag_id">';
        while ($row = conn()->query("SELECT id, tag_name FROM tags")->fetch_assoc()) {
            echo '<option value="' . $row['id'] . '">' . $row['tag_name'] . '</option>';
        }
        echo '</select><br>
                    <label for="id_post">Choose Post to attach</label><br>
                    <select name="id_post">';
        while ($row = conn()->query("SELECT id, topic FROM blogs")->fetch_assoc()) {
            echo '<option value="' . $row['id'] . '">' . $row['topic'] . '</option>';
        }
        echo '</select><br>
                    <input type="submit" name="attach_tag" value="Attach Tag">
                  </form>';
        return;
    }

    $stmt = conn()->prepare("INSERT INTO blog_tags (blog_id, tag_id) VALUES (?, ?)");

    if (!$stmt) {
        echo "Error preparing statement.";
        return;
    }

    $stmt->bind_param("ii", $_POST['id_post'], $_POST['tag_id']);
    $stmt->execute();
    $stmt->close();
    returnToHome();
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
        echo '<input name="' . ($type == 'blog' ? 'blog_id' : 'doc_id') . '" type="hidden" value="' . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . '">';
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
function addTag()
{
    middleware();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tag_name'])) {
        $conn = conn();
        $stmt = $conn->prepare("INSERT INTO tags (tag_name) VALUES (?)");
        $stmt->bind_param("s", $_POST['tag_name']);

        if ($stmt->execute()) {
            returnToHome();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        return;
    }

    echo '<form action="/addTag" method="post">
                <label for="tag_name">Tag Name:</label><br>
                <input type="text" id="tag_name" name="tag_name"><br><br>
                <input type="submit" value="Submit">
              </form>';
}

function middleware()
{
    session_start();
    global $hashed_password;

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
?>
    <a href="../">Home</a><br>
    <a href="../assets/img/">Acces Image Storaage</a><br>
    <a href="/attachTag">Attach tag</a><br>
    <a href="/addBlog">Add blog</a><br>
    <a href="/addTag">Add tag</a><br>
    <a href="/addImage">Add Image</a><br>
    <a href="/editBlog">Edit Blog</a><br>
    <a href="/editTag">Edit Tag</a><br>
    <a href="/deleteAttachTag">Delete Attached tag</a><br>
    <a href="/deleteTag">Delete tag</a><br>
    <a href="/deleteBlog">Delete Blog</a><br>
    <a href="/deleteImage">Delete Image</a><br>
<?php
}

function editBlog()
{
    middleware();
    echo '<form action="/editBlogPage" method="POST">
                <label for="blog_id">Choose Blogs to edit</label><br>
                <select name="blog_id">';
    while ($row = conn()->query("SELECT id, topic FROM blogs")->fetch_assoc()) {
        echo "<option value='" . $row['id'] . "'>" . $row['topic'] . "</option>";
    }
    echo '</select><br>
                <input type="submit" name="delete" value="Go">
              </form>';
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

function editTag()
{
    middleware();
    $conn = conn();
    $result = $conn->query("SELECT id, tag_name FROM tags");

    if ($result && $result->num_rows > 0) {
        echo '<form action="/editTagPage" method="POST">
                <label for="tag_id">Choose Tag to Edit</label><br>
                <select name="tag_id">';
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($row['tag_name'], ENT_QUOTES, 'UTF-8') . "</option>";
        }

        echo '</select><br>
                <input type="submit" value="Go">
              </form>';
    } else {
        echo "No tags found.";
    }
    $conn->close();
}


function editTagPage()
{
    middleware();
    $stmt = mysqli_prepare(conn(), "SELECT tag_name FROM tags WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "s", $_POST['tag_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<form action="/editTagHandle" method="POST">
                    <label for="tag_name">Tag Name:</label><br>
                    <input type="text" id="tag_name" name="tag_name" value="' . htmlspecialchars($row['tag_name'], ENT_QUOTES, 'UTF-8') . '"><br><br>
                    <input name="tag_id" type="hidden" value="' . htmlspecialchars($_POST['tag_id'], ENT_QUOTES, 'UTF-8') . '">
                    <input type="submit" value="Go">
                  </form>';
    }
}

function editTagHandle()
{
    middleware();
    $stmt = conn()->prepare("UPDATE tags SET tag_name = ? WHERE id = ?");
    $stmt->bind_param("si", $_POST['tag_name'], $_POST['tag_id']);
    $stmt->execute();
    returnToHome();
}

function deleteAttachTag()
{
    middleware();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        $stmt = conn()->prepare("DELETE FROM blog_tags WHERE blog_id = ? AND tag_id = ?");
        $stmt->bind_param("ii", $_POST['id_post'], $_POST['tag_id']);
        $stmt->execute();
        $stmt->close();
        returnToHome();
        return;
    }

    echo '<form action="/deleteAttachTag" method="POST">
                <label for="tag_id">Choose Tag to attach</label><br>
                <select name="tag_id">';
    while ($row = conn()->query("SELECT id, tag_name FROM tags")->fetch_assoc()) {
        echo "<option value='" . $row['id'] . "'>" . $row['tag_name'] . "</option>";
    }
    echo '</select><br>
                <label for="id_post">Choose Post to be attached</label><br>
                <select name="id_post">';
    while ($row = conn()->query("SELECT id, topic FROM blogs")->fetch_assoc()) {
        echo "<option value='" . $row['id'] . "'>" . $row['topic'] . "</option>";
    }
    echo '</select><br>
                <input type="submit" name="delete" value="Go">
              </form>';
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

    echo '<form action="/deleteBlog" method="POST">
                    <label for="blog_id">Choose Blogs to Delete</label><br>
                    <select name="blog_id">';
    while ($row = conn()->query("SELECT id, topic FROM blogs")->fetch_assoc()) {
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

    $files = glob($imageDirectory . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
?>
    <form method="post" action="/deleteImage">
        <label for="image">Select Image to Delete:</label>
        <select name="image" id="image">
            <?php
            foreach ($files as $file) {
                $fileName = basename($file);
                echo "<option value=\"$fileName\">$fileName</option>";
            }
            ?>
        </select><br>
        <input type="submit" value="Delete">
    </form>
<?php
}

function deleteTag()
{
    middleware();
    if (!isset($_POST['delete'])) {
        echo '<form action="/deleteTag" method="POST">
                <label for="tag_id">Choose Tag to Delete:</label><br>
                <select name="tag_id">';
        while ($row = conn()->query("SELECT id, tag_name FROM tags")->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['tag_name'] . "</option>";
        }
        echo '</select><br>
                  <input type="submit" name="delete" value="Delete">
                  </form>';
        return;
    }

    $stmt = conn()->prepare("CALL delete_tag(?)");
    $stmt->bind_param("i", $_POST['tag_id']);
    $stmt->execute();
    returnToHome();
}

function tag($tag)
{
    global $name, $domain;
    head('Tag Specify', $name, $tag, $tag, $domain);
    echo "<div class='pt-12 pb-4'>";
    nav();
    echo "<div class='px-4 pt-4 prose prose-indigo'>
                    <h1 class='text-center mt-6 mb-6'>{$tag} Tag</h1>";
    $stmt = conn()->prepare("SELECT b.topic, b.shortdesc, b.created_at FROM blogs b JOIN blog_tags bt ON b.id = bt.blog_id JOIN tags t ON bt.tag_id = t.id WHERE t.tag_name = ? GROUP BY b.topic, b.shortdesc, b.created_at");
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
    $result = $conn->query("SELECT * FROM tags");
    return $result;
}
function migrate()
{
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
        <title>' . htmlspecialchars($doc_title, ENT_QUOTES, 'UTF-8') . '</title>
        <meta name="description" content="' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '">
        <meta name="keywords" content="' . htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') . '">
        <meta name="author" content="' . htmlspecialchars($author, ENT_QUOTES, 'UTF-8') . '">
        <meta property="og:title" content="' . htmlspecialchars($doc_title, ENT_QUOTES, 'UTF-8') . '">
        <meta property="og:description" content="' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '">
        <meta property="og:image" content="assets/img/profile.jpg">
        <meta property="og:url" content="' . htmlspecialchars($domain, ENT_QUOTES, 'UTF-8') . '">
        <meta property="og:type" content="website">
        <link rel="stylesheet" href="../assets/css/tailwind.min.css">
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
        <link rel="stylesheet" href="../assets/css/atom-one-dark.min.css">
        <script src="../assets/js/highlight.min.js"></script>
        <link rel="stylesheet" href="../assets/css/viewer.min.css">
        <script src="../assets/js/viewer.min.js"></script>
        <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
    </head>
    <body class="font-sans mx-auto max-w-prose">';
}
function listen()
{
    global $url;
    addRoute('/', 'home');
    addRoute('/migrate', 'migrate');
    addRoute('/blog', 'blogList');
    addRoute('/blog/([\w-]+)', 'blog');
    addRoute('/tag', 'tagList');
    addRoute('/attachTag', 'attachTag');
    addRoute('/addBlog', 'addBlog');
    addRoute('/addTag', 'addTag');
    addRoute('/addImage', 'addImage');
    addRoute('/manage', 'manage');
    addRoute('/editBlog', 'editBlog');
    addRoute('/editBlogPage', 'editBlogPage');
    addRoute('/editBlogHandle', 'editBlogHandle');
    addRoute('/editTag', 'editTag');
    addRoute('/editTagPage', 'editTagPage');
    addRoute('/editTagHandle', 'editTagHandle');
    addRoute('/deleteAttachTag', 'deleteAttachTag');
    addRoute('/deleteBlog', 'deleteBlog');
    addRoute('/deleteImage', 'deleteImage');
    addRoute('/deleteTag', 'deleteTag');
    addRoute('/tag/([\w-]+)', 'tag');
    handleRequest($url);
}
listen();
