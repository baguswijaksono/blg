<?php 

function loadEnv($filePath)
{
    if (file_exists($filePath)) {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, '#') === 0 || empty($line)) {
                continue;
            }
            list($key, $value) = explode('=', $line, 2) + [NULL, NULL];
            if (!is_null($key)) {
                $key = trim($key);
                $value = trim($value);
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

loadEnv(__DIR__ . '/.env');

class blog
{
    public $domain = "https://yourdns";
    private $routes = [];
    public $url;
    public $servername;
    public $username;
    public $password;
    public $dbname;
    public $conn;
    private $name;
    public $hashed_password = '$2y$10$v4Zr9MgjWSDa1vM1l6RHgOeX/joovOcp217fxNvhB5YdFu1Ukak5O';
    private $about;
    private $linktreedata;
    public function route($route, $handler)
    {
        $this->routes[$route] = $handler;
    }
    public function handleRequest($url)
    {
        foreach ($this->routes as $route => $handler) {
            if (preg_match("#^$route$#", $url, $matches)) {
                array_shift($matches);
                return call_user_func_array($handler, $matches);
            }
        }
        http_response_code(404);
        $this->nf();
    }
    public function listen()
    {
        $this->route('/', function () {
            $this->home();
        });
        $this->route('/blog', function () {
            $this->bloglist();
        });
        $this->route('/blog/([\w-]+)', function ($topic) {
            $this->blog($topic);
        });
        $this->route('/tag', function () {
            $this->taglist();
        });
        $this->route('/attachTag', function () {
            $this->attachTag();
        });
        $this->route('/addBlog', function () {
            $this->addBlog();
        });
        $this->route('/addTag', function () {
            $this->addTag();
        });
        $this->route('/addImage', function () {
            $this->addImage();
        });
        $this->route('/manage', function () {
            $this->manage();
        });
        $this->route('/editBlog', function () {
            $this->editBlog();
        });
        $this->route('/editBlogPage', function () {
            $this->editBlogPage();
        });
        $this->route('/editBlogHandle', function () {
            $this->editBlogHandle();
        });
        $this->route('/editTag', function () {
            $this->editTag();
        });
        $this->route('/editTagPage', function () {
            $this->editTagPage();
        });
        $this->route('/editTagHandle', function () {
            $this->editTagHandle();
        });
        $this->route('/deleteAttachTag', function () {
            $this->deleteAttachTag();
        });
        $this->route('/deleteBlog', function () {
            $this->deleteBlog();
        });
        $this->route('/deleteImage', function () {
            $this->deleteImage();
        });
        $this->route('/deleteTag', function () {
            $this->deleteTag();
        });
        $this->route('/tag/([\w-]+)', function ($tag) {
            $this->tag($tag);
        });
        $this->handleRequest($this->url);
    }
    public function __construct()
    {
        $this->servername = getenv('DB_SERVERNAME') ?: 'localhost';
        $this->username = getenv('DB_USERNAME') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->dbname = getenv('DB_NAME') ?: 'blog';

        $this->conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        session_start();
        $this->url = $_SERVER['REQUEST_URI'];
        $this->name = 'Bagus Muhammad Wijaksono';
        $this->about = 'nuh uh';
        $this->linktreedata = [
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
    }

    public function __destruct()
    {
        mysqli_close($this->conn);
    }

    public function nf()
    {
        $this->header('Not Found',$this->name,"Not Found" ,"Notfound",$this->domain); ?>

        <body class='font-sans mx-auto max-w-prose'>
            <div class='pt-12 pb-4'>
                <div class='bg-white min-h-full px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8'>
                    <div class='max-w-max mx-auto'>
                        <main class='sm:flex'>
                            <p class='text-4xl font-extrabold text-indigo-600 sm:text-5xl'>404</p>
                            <div class='sm:ml-6'>
                                <div class='sm:border-l sm:border-gray-200 sm:pl-6'>
                                    <h1 class='text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl'>Page not found
                                    </h1>
                                    <p class='mt-1 text-base text-gray-500'>Please check the URL in the address bar and try
                                        again.</p>
                                </div>
                                <div class='mt-10 flex space-x-3 sm:border-l sm:border-transparent sm:pl-6'>
                                    <a href='../'
                                        class='inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'>Go
                                        back home</a>
                                </div>
                            </div>
                        </main>
                    </div>
                    <div class='w-1/4 w-1/2 w-1/3 w-3/4 w-4/5'></div>
                </div>
            </div>
        </body>
        <?php
    }
    public function header($doc_title, $author , $description, $keyword, $domain)
    { ?>
        <!DOCTYPE html>
        <html lang=" en-US">

        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta charset="utf-8">
            <title><?php echo $doc_title; ?></title>

            <!-- SEO Meta Tags -->
            <meta name="description" content="<?php echo $description; ?>">
            <meta name="keywords" content="<?php echo $keyword; ?>">
            <meta name="author" content="<?php echo $author; ?>">

            <!-- Open Graph Meta Tags -->
            <meta property="og:title" content="<?php echo $doc_title; ?>">
            <meta property="og:description" content="<?php echo $description; ?>">
            <meta property="og:image" content="assets/img/profile.jpg">
            <meta property="og:url" content="<?php echo $domain; ?>">
            <meta property="og:type" content="website">

            <!-- TailwindCSS and Inter Font-->
            <link rel="stylesheet" href="../assets/css/tailwind.css">
            <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
            <!-- fontawesome -->
            <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
            <!-- highlightjs -->
            <link rel="stylesheet" href="../assets/css/atom-one-dark.min.css">
            <script src="../assets/js/highlight.min.js"></script>
            <!-- viewerjs -->
            <link rel="stylesheet" href="../assets/css/viewer.min.css">
            <script src="../assets/js/viewer.min.js"></script>
            <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
        </head>
        <?php
    }

    public function footer()
    { ?>
        <script>
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
            
  document.addEventListener('DOMContentLoaded', function () {
    var gallery = document.getElementById('gallery');
    var viewer = new Viewer(gallery, {
      // Options (optional)
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

        </html>
        <?php
    }
    public function getAllTags($conn)
    {
        $sql = "SELECT * FROM tags";
        $result = $conn->query($sql);
        return $result;
    }
    public function getBlogs($conn, $project)
    {
        $sql = "SELECT * FROM blogs WHERE topic = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $project);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result;
    }
    public function getTags($conn, $blog_id)
    {
        $sql_get_tags = "SELECT tags.tag_name\n FROM tags\n INNER JOIN blog_tags ON tags.id = blog_tags.tag_id\n WHERE blog_tags.blog_id = ?";
        $stmt = $conn->prepare($sql_get_tags);
        $stmt->bind_param('i', $blog_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }
    public function getViews($conn, $content_id)
    {
        $sql = "SELECT views_count\n FROM views\n WHERE content_id = ?";
        $stmt = $conn->prepare($sql);
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
    public function incrementViews($conn, $content_id)
    {
        $sql = "SELECT views_count\n FROM views\n WHERE content_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $content_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $updateSql = "UPDATE views SET views_count = views_count + 1 WHERE content_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param('i', $content_id);
            $updateStmt->execute();
        } else {
            $insertSql = "INSERT INTO views (content_id, views_count) VALUES ( ?, 1)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param('i', $content_id);
            $insertStmt->execute();
        }
    }

    public function blog($topic)
{
    $result = $this->getBlogs($this->conn, $topic);
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $blog_id = $row["id"];
            $this->incrementViews($this->conn, $blog_id);
            $getTagresult = $this->getTags($this->conn, $blog_id);
            
            $tags = [];
            if ($getTagresult->num_rows > 0) {
                while ($tag = $getTagresult->fetch_assoc()) {
                    $tags[] = $tag['tag_name'];
                }
            }

            $tagsString = implode(', ', $tags);

            $this->header('Blog', $this->name, $row['shortdesc'], $tagsString,$this->domain);
            ?>
            
            <body class="font-sans mx-auto max-w-prose">
                <div class="pt-12 pb-4">
                    <?php $this->navbar(); ?>
                    <div class="px-4 pt-4 prose prose-indigo">
                        <h1 class="text-center mt-6 mb-6">
                            <?php echo $row["title"]; ?>
                        </h1>
                        <div class="text-center">
                            <?php 
                            foreach ($tags as $tag) {
                                ?>
                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <a class="!no-underline" href="../tag/<?php echo $tag; ?>">
                                        <?php echo $tag; ?>
                                    </a>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php echo $row["hypertext"]; ?>
                    </div>
                </div>
            
            <?php
        }
        $this->footer();
    } else {
        $this->nf();
    }
}

public function navbar()
{ ?>
    <nav class="flex flex-wrap items-center justify-center px-2 mb-6">
        <div class="container px-4 mx-auto flex flex-wrap items-center justify-center">
            <div class="w-full relative flex justify-center">
                <button type="button" onclick="toggleNavbar('navbar')" class="rounded-md inline-flex items-center justify-center text-gray-500 md:hidden" aria-expanded="false">
                    <span class="sr-only">Open menu</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            <div class="flex hidden md:block" id="navbar">
                <div class="flex flex-col md:flex-row mr-auto w-full">
                    <?php 
                    $menuItems = ['Home' => '/', 'Blog' => '/blog', 'Tag' => '/tag'];
                    $currentEndpoint = rtrim($_SERVER['REQUEST_URI'], '/');
                    foreach ($menuItems as $label => $url) {
                        if ($currentEndpoint != rtrim($url, '/')) {
                            $isActive = ($currentEndpoint == rtrim($url, '/')) ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900'; ?>
                            <a class="text-sm <?= $isActive ?> px-3 py-2 lg:py-1 mx-auto uppercase <?= $isActive == 'text-gray-900' ? 'active' : '' ?>" href="<?= $url ?>"><?= $label ?></a><?php
                        }
                    } ?>
                </div>
            </div>
        </div>
    </nav>
<?php }
    
    public function bloglist()
    {
        $this->header('Blog List', $this->name,"Blog List", "Blog List",$this->domain); ?>

        <body class="font-sans mx-auto max-w-prose">
            <div class="pt-12 pb-4"><?php $this->navbar() ?>
                <div class="px-4 pt-4 prose prose-indigo">
                    <h1 class="text-center mt-6 mb-6">Blog</h1>
                    <div><?php $sql = "SELECT * FROM blogs WHERE is_public = 1";
                    $result = mysqli_query($this->conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $blog_id = $row['id'];
                            $topic = $row['topic'];
                            $createdAt = $row['created_at'];
                            $dateTime = new DateTime($createdAt);
                            $formattedDate = $dateTime->format('F j, Y'); ?>
                                <div class="pt-3.5"><a class="!no-underline" href="<?php echo '/blog/' . $topic ?>">
                                        <h3 class="text-sm text-gray-700"><?php echo $topic; ?>
                                            <div
                                                class="inline-flex items-center px-2.5 py-0.25 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                <?php echo $this->getViews($this->conn, $blog_id) ?> Views
                                            </div>
                                        </h3>
                                    </a>
                                    <p class="max-w-[40ch] text-xs text-gray-500"><?php echo $row['shortdesc'];
                                    ?> <br> <br> <small class="text-gray-400"><?php echo $formattedDate; ?></small></p>
                                </div><?php
                        }
                    } ?>
                    </div>
                </div>
            </div><?php
        $this->footer();
    }
    public function home()
    {
        $this->header($this->name, $this->name, "Personal website of ".$this->name.", showcasing blog posts, projects, and professional profiles.","".$this->name." personal website, blog, projects, professional profiles",$this->domain); ?>

            <body class="font-sans mx-auto max-w-prose">
                <div class="pt-12 pb-4"><?php $this->navbar() ?>
                    <div class="mx-auto text-center prose prose-indigo">
                        <div><img class="object-cover mx-auto h-36 w-36 rounded-full" src="assets\img\profile.jpg">
                            <h1><?php echo $this->name ?></h1>
                        </div>
                        <p class="text-gray-500 pb-4"><?php echo $this->about ?></p>
                    </div>
                </div>
                <section>
                    <div class="flex flex-wrap text-center">
                        <div class="w-full pt-4">
                            <div><?php
                            if ($this->linktreedata !== null) {
                                foreach ($this->linktreedata as $row) { ?><a href="<?php echo $row['url'] ?>"
                                            target="_blank">
                                            <button
                                                class="bg-white text-lightBlue-600 font-normal h-10 w-10 items-center justify-center align-center rounded-full outline-none focus:outline-none mr-2"
                                                type="button"><i class="fab fa-<?php echo $row['platform_name'] ?>"></i></button></a><?php }
                            } ?>
                            </div>
                        </div>
                    </div>
                </section>
                <?php $this->footer();
    }
    public function addBlog()
    {
        if (
            isset($_SESSION['original_password'])
            && password_verify($_SESSION['original_password'], $this->hashed_password)
        ) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $topic = $_POST['topic'];
                $docname = $_POST['docname'];
                $title = $_POST['title'];
                $shortdesc = $_POST['shortdesc'];
                $hypertext = $_POST['hypertext'];
                $is_public = $_POST['is_public']; // Add the is_public field
                $sql = "INSERT INTO blogs (topic, docname, title, shortdesc, hypertext, is_public) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($this->conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssssi", $topic, $docname, $title, $shortdesc, $hypertext, $is_public);

                if (mysqli_stmt_execute($stmt)) {
                    $this->returnToHome();
                } else {
                    $this->returnToHome();
                }
            } else {
                $this->display_form('blog', '/addBlog', 'add');
            }
        } else {
            $this->returnToHome();
        }
    }
    public function taglist()
    {
        $this->header('Tag List' , $this->name,"Tag list for blog" , "Tag List",$this->domain); ?>

                <body class="font-sans mx-auto max-w-prose">
                    <div class="pt-12 pb-4"><?php $this->navbar() ?>
                        <div class="px-4 pt-4 prose prose-indigo">
                            <h1 class="text-center mt-6 mb-6">Tags</h1>
                            <?php $result = $this->getAllTags($this->conn);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $tagName = $row["tag_name"]; ?>
                                    <div
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <a class="!no-underline" href="<?php echo '/tag/' . $tagName; ?>"><?php echo $tagName; ?></a>
                                    </div>
                                    <?php
                                }
                            } ?>
                        </div>
                    </div>
                    <?php $this->footer();
    }
    public function tag($tag)
    {
        $this->header('Tag Specify', $this->name,$tag,$tag,$this->domain); ?>

                    <body class="font-sans mx-auto max-w-prose">
                        <div class="pt-12 pb-4"><?php $this->navbar() ?>
                            <div class="px-4 pt-4 prose prose-indigo">
                                <h1 class="text-center mt-6 mb-6"><?php echo $tag; ?> Tag</h1>
                                <?php $sqlBlogs = "SELECT b.topic, b.shortdesc, b.created_at FROM blogs b JOIN blog_tags bt ON b.id = bt.blog_id JOIN tags t ON bt.tag_id = t.id WHERE t.tag_name = '$tag' GROUP BY b.topic, b.shortdesc, b.created_at";
                                $resultBlogs = $this->conn->query($sqlBlogs);
                                if ($resultBlogs) { ?>
                                <?php while ($row = $resultBlogs->fetch_assoc()) { ?>
                                        <div class="pt-3.5"><a class="!no-underline" href="../blog/<?php echo $row['topic'] ?>">
                                                <h3 class="text-sm text-gray-700"><?php echo $row['topic']; ?>
                                                    <div
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        blog</div>
                                                </h3>
                                            </a>
                                            <p class="max-w-[40ch] text-xs text-gray-500"><?php
                                            echo $row['shortdesc'];
                                            ?> <br> <br> <small class="text-gray-400"><?php $dateTime = new DateTime($row['created_at']);
                                             $formattedDate = $dateTime->format('F j, Y');
                                             echo $formattedDate; ?></small></p>
                                        </div><?php } ?><?php } ?>
                            </div>
                        </div>
                        <?php
                        $this->footer();
    }
    public function addImage()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
                $uploadDir = "assets/img/";
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = uniqid("image_") . "." . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
                    $this->returnToHome();
                } else {
                    $this->returnToHome();
                }
            } else {
                $this->returnToHome();
            }
        } else {
            if (isset($_SESSION['original_password']) && password_verify($_SESSION['original_password'], $this->hashed_password)) {
                ?>
                                <form action="/addImage" method="post" enctype="multipart/form-data">
                                    <label for="image">Select Image:</label>
                                    <input type="file" name="image" id="image" accept="image/*"><br>
                                    <input type="submit" value="Upload">
                                </form>
                                <?php
            } else {
                $this->returnToHome();
            }
        }
    }
    public function addTag()
    {
        if (isset($_SESSION['original_password']) && password_verify($_SESSION['original_password'], $this->hashed_password)) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tag_name'])) {
                $tag_name = $_POST['tag_name'];
                $sql = "INSERT INTO `tags` (`id`, `tag_name`) VALUES (NULL, ?);";
                $stmt = mysqli_prepare($this->conn, $sql);
                mysqli_stmt_bind_param($stmt, "s", $tag_name);

                if (mysqli_stmt_execute($stmt)) {
                    $this->returnToHome();
                } else {
                    echo "Error: " . mysqli_error($this->conn);
                }
            } else {
                ?>
                                <form action="/addTag" method="post">
                                    <label for="tag_name">Tag Name:</label><br>
                                    <input type="text" id="tag_name" name="tag_name"><br><br>
                                    <input type="submit" value="Submit">
                                </form>
                                <?php
            }
        } else {
            $this->returnToHome();
        }
    }

    public function bootstrapHead($title)
    { ?>
                        <!DOCTYPE html>
                        <html lang="en">

                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title><?php echo $title; ?></title>
                            <link href="assets/css/bootstrap.min.css" rel="stylesheet">
                            <script src="assets/js/tinymce.min.js"></script>
                        </head>
                        <?php
    }

    public function deleteAttachTag()
    {
        if (password_verify($_SESSION['original_password'], $this->hashed_password)) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
                $id_post = $_POST['id_post'];
                $tag_id = $_POST['tag_id'];
                $sql = "DELETE FROM `blog_tags` WHERE `blog_id` = ? AND `tag_id` = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ii", $id_post, $tag_id);
                $stmt->execute();
                $stmt->close();
                $this->returnToHome();
            } else {
                $getTagquery = "SELECT * FROM tags";
                $getTagresult = $this->conn->query($getTagquery);
                $getpostquery = "SELECT * FROM blogs";
                $getpostresult = $this->conn->query($getpostquery);
                ?>
                                <form action="/deleteAttachTag" method="POST">
                                    <label for="tag_id">Choose Tag to attach</label><br>
                                    <select name="tag_id">
                                        <?php while ($row = $getTagresult->fetch_assoc()) {
                                            echo "<option value='" . $row['id'] . "'>" . $row['tag_name'] . "</option>";
                                        } ?>
                                    </select><br>
                                    <label for="id_post">Choose Post to be attached</label><br>
                                    <select name="id_post">
                                        <?php while ($row = $getpostresult->fetch_assoc()) {
                                            echo "<option value='" . $row['id'] . "'>" . $row['topic'] . "</option>";
                                        } ?>
                                    </select><br>
                                    <input type="submit" name="delete" value="Go">
                                </form>
                                <?php
            }
        } else {
            $this->returnToHome();
        }
    }
    public function deleteTag()
    {
        if (password_verify($_SESSION['original_password'], $this->hashed_password)) {
            if (isset($_POST['delete'])) {
                $tag_id = $_POST['tag_id'];
                $stmt = $this->conn->prepare("CALL delete_tag(?)");
                $stmt->bind_param("i", $tag_id);
                $result = $stmt->execute();

                if ($result) {
                    $this->returnToHome();
                } else {
                    $this->returnToHome();
                }
            } else {
                $query = "SELECT * FROM tags";
                $result = $this->conn->query($query);
                ?>
                                <form action="/deleteTag" method="POST">
                                    <label for="tag_id">Choose Tag to Delete:</label><br>
                                    <select name="tag_id">
                                        <?php while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . $row['id'] . "'>" . $row['tag_name'] . "</option>";
                                        } ?>
                                    </select><br>
                                    <input type="submit" name="delete" value="Delete">
                                </form>
                                <?php
            }
        } else {
            $this->returnToHome();
        }
    }
    public function attachTag()
    {
        if (
            isset($_SESSION['original_password']) &&
            password_verify($_SESSION['original_password'], $this->hashed_password)
        ) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tag_id'], $_POST['id_post'])) {
                $tag_id = $_POST['tag_id'];
                $id_post = $_POST['id_post'];
                if (is_numeric($tag_id) && is_numeric($id_post)) {
                    $sql = "INSERT INTO `blog_tags` (`blog_id`, `tag_id`) VALUES (?, ?)";
                    $stmt = $this->conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("ii", $id_post, $tag_id);
                        $stmt->execute();
                        $stmt->close();
                        $this->returnToHome();
                    } else {
                        echo "Error preparing statement.";
                    }
                } else {
                    echo "Invalid tag_id or id_post.";
                }
            } else {
                $getTagquery = "SELECT * FROM tags";
                $getTagresult = $this->conn->query($getTagquery);
                $getpostquery = "SELECT * FROM blogs";
                $getpostresult = $this->conn->query($getpostquery);
                ?>
                                <form action="/attachTag" method="POST">
                                    <label for="tag_id">Choose Tag to attach</label><br>
                                    <select name="tag_id">
                                        <?php while ($row = $getTagresult->fetch_assoc()): ?>
                                            <option value="<?= $row['id'] ?>"><?= $row['tag_name'] ?></option>
                                        <?php endwhile; ?>
                                    </select><br>
                                    <label for="id_post">Choose Post to attach</label><br>
                                    <select name="id_post">
                                        <?php while ($row = $getpostresult->fetch_assoc()): ?>
                                            <option value="<?= $row['id'] ?>"><?= $row['topic'] ?></option>
                                        <?php endwhile; ?>
                                    </select><br>
                                    <input type="submit" name="attach_tag" value="Attach Tag">
                                </form>
                                <?php
            }
        } else {
            $this->returnToHome();
        }
    }

    public function display_form($type, $form_action, $operation, $row = null)
    {
        $pageTitle = ($operation == 'add') ? "Add $type Page" : "Edit $type Page";
        $submitButtonText = ($operation == 'add') ? 'Submit' : 'Update';
        $typepost = ($type == 'blog') ? 'topic' : 'project';
        $this->bootstrapHead($pageTitle); ?>

                        <body>
                            <div class="container">
                                <form action="<?php echo $form_action; ?>" method="post">
                                    <label class="form-label pt-3" for="topic">Topic:</label>
                                    <br>
                                    <input class="form-control" type="text" id="<?php echo $typepost; ?>"
                                        name="<?php echo $typepost; ?>" value="<?php echo ($row) ? $row[$typepost] : ''; ?>">
                                    <br>
                                    <label class="form-label" for="docname">Docname:</label>
                                    <br>
                                    <input class="form-control" type="text" id="docname" name="docname"
                                        value="<?php echo ($row) ? $row['docname'] : ''; ?>">
                                    <br>
                                    <label class="form-label" for="title">Title:</label>
                                    <br>
                                    <input class="form-control" type="text" id="title" name="title"
                                        value="<?php echo ($row) ? $row['title'] : ''; ?>">
                                    <br>
                                    <label class="form-label" for="hypertext">Hypertext:</label>
                                    <br>
                                    <textarea id="hypertext" name="hypertext" rows="4"
                                        cols="50"><?php echo ($row) ? $row['hypertext'] : ''; ?></textarea>
                                    <br>
                                    <label class="form-label" for="shortdesc">Short Description:</label>
                                    <br>
                                    <textarea class="form-control" id="shortdesc" name="shortdesc" rows="4"
                                        cols="50"><?php echo ($row) ? $row['shortdesc'] : ''; ?></textarea>
                                    <br>
                                    <?php if ($operation == 'edit'): ?>             <?php if ($type == 'blog'): ?>
                                            <input name="blog_id" type="hidden" value="<?php echo $row['id'] ?>"><?php endif; ?>
                                        <?php if ($type == 'doc'): ?>
                                            <input name="doc_id" type="hidden"
                                                value="<?php echo $row['id'] ?>"><?php endif; ?><?php endif; ?>
                                    <label class="form-check-label" for="is_public">Is Public:</label>
                                    <select class="form-control" id="is_public" name="is_public">
                                        <option value="0" <?php echo ($row && $row['is_public'] == 0) ? 'selected' : ''; ?>>0</option>
                                        <option value="1" <?php echo ($row && $row['is_public'] == 1) ? 'selected' : ''; ?>>1</option>
                                    </select>
                                    <br>
                                    <input class="btn btn-dark" type="submit" value="<?php echo $submitButtonText; ?>">
                                </form>
                                <script>
                                    tinymce.init({
                                        selector: '#hypertext',
                                        height: 500,
                                        plugins: 'code',
                                        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | code',
                                    });
                                </script>
                            </div>
                        </body>

                        </html>
                        <?php
    }

    public function editBlog()
    {
        if (password_verify($_SESSION['original_password'], $this->hashed_password)) {
            $query = "SELECT * FROM blogs";
            $result = $this->conn->query($query); ?>
                            <form action="/editBlogPage" method="POST"><label for="blog_id">Choose Blogs to
                                    edit</label><br><select name="blog_id"><?php while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['topic'] . "</option>";
                                    } ?></select><br><input type="submit" name="delete" value="Go"></form>
                            <?php
        } else {
            $this->returnToHome();
        }
    }
    public function editBlogPage()
    {
        if (password_verify($_SESSION['original_password'], $this->hashed_password)) {
            $blog_id = $_POST['blog_id'];
            $sql = "SELECT * FROM blogs WHERE id = ?";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $blog_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $this->display_form('blog', '/editBlogHandle', 'edit', $row);
            }
        } else {
            $this->returnToHome();
        }
    }
    public function editTag()
    {
        if (password_verify($_SESSION['original_password'], $this->hashed_password)) {
            $query = "SELECT * FROM tags";
            $result = $this->conn->query($query); ?>
                            <form action="/editTagPage" method="POST"><label for="tag_id">Choose Docs to
                                    edit</label><br><select name="tag_id"><?php while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['tag_name'] . "</option>";
                                    } ?></select><br><input type="submit" name="delete" value="Go"></form>
                            <?php
        } else {
            $this->returnToHome();
        }
    }
    public function editTagPage()
    {
        if (password_verify($_SESSION['original_password'], $this->hashed_password)) {
            $tag_id = $_POST['tag_id'];
            $sql = "SELECT * FROM tags WHERE id = ?";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $tag_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) { ?>
                                <form action="/editTagHandle" method="POST"><label for="tag_name">tag_name:</label><br>
                                    <input type="text" id="tag_name" name="tag_name" value="<?php echo $row['tag_name'] ?>"><br><br>
                                    <input name="tag_id" type="hidden" value="<?php echo $row['id'] ?>"><?php } ?>
                                <input type="submit" value="Go">
                            </form>
                            <?php
        } else {
            $this->returnToHome();
        }
    }
    public function editTagHandle()
    {
        $tag_id = $_POST['tag_id'];
        $tag_name = $_POST['tag_name'];
        if (password_verify($_SESSION['original_password'], $this->hashed_password)) {
            $sql = "UPDATE `tags` SET `tag_name` = ? WHERE `tags`.`id` = ?;";
            $stmt = mysqli_prepare($this->conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "si", $tag_name, $tag_id);
                if (mysqli_stmt_execute($stmt)) {
                    $this->returnToHome();
                } else {
                    $this->returnToHome();
                }
            } else {
                $this->returnToHome();
            }
        } else {
            $this->returnToHome();
        }
    }
    public function editBlogHandle()
    {
        $blog_id = $_POST['blog_id'];
        $docname = $_POST['docname'];
        $topic = $_POST['topic'];
        $title = $_POST['title'];
        $hypertext = $_POST['hypertext'];
        $shortdesc = $_POST['shortdesc'];
        $is_public = $_POST['is_public']; 
        if (password_verify($_SESSION['original_password'], $this->hashed_password)) {
            $sql = "UPDATE `blogs` SET `topic` = ?, `docname` = ?, `title` = ?, `hypertext` = ?, `shortdesc` = ?, `is_public` = ? WHERE `blogs`.`id` = ?;";
            $stmt = mysqli_prepare($this->conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssii", $topic, $docname, $title, $hypertext, $shortdesc, $is_public, $blog_id);
                if (mysqli_stmt_execute($stmt)) {
                    $this->returnToHome();
                } else {
                    $this->returnToHome();
                }
            } else {
                $this->returnToHome();
            }
        } else {
            $this->returnToHome();
        }
    }
    public function deleteImage()
    {
        if (password_verify($_SESSION['original_password'], $this->hashed_password)) {
            $imageDirectory = 'assets/img/';

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image'])) {
                $selectedImage = $_POST['image'];
                $imageFilePath = $imageDirectory . $selectedImage;

                if (file_exists($imageFilePath)) {
                    if (unlink($imageFilePath)) {
                        $this->returnToHome();
                        return;
                    }
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
        } else {
            $this->returnToHome();
        }
    }

    public function manage()
    {
        if (isset($_SESSION['original_password'])) { ?>
                            <a href="../">Home</a>
                            <br>
                            <a href="../assets/img/">Acces Image Storaage</a>
                            <br>
                            <a href="/attachTag">Attach tag</a>
                            <br>
                            <a href="/addBlog">Add blog</a>
                            <br>
                            <a href="/addTag">Add tag</a>
                            <br>
                            <a href="/addImage">Add Image</a>
                            <br>
                            <a href="/editBlog">Edit Blog</a><br>
                            <a href="/editTag">Edit Tag</a><br>
                            <a href="/deleteAttachTag">Delete Attached tag</a><br>
                            <a href="/deleteTag">Delete tag</a><br>
                            <a href="/deleteBlog">Delete Blog</a><br>
                            <a href="/deleteImage">Delete Image</a><br>
                        <?php } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'];
            if (password_verify($password, $this->hashed_password)) {
                $_SESSION['original_password'] = $password;
                header('Location: ../manage');
                exit;
            } else {
                $this->returnToHome();
            }
        } else { ?>
                            <form action="" method="post">
                                <input type="password" name="password" id="password" placeholder="Password">
                                <button type="submit">Unlock</button>
                            </form>
                        <?php }
    }

    public function deleteBlog()
    {
        if (password_verify($_SESSION['original_password'], $this->hashed_password)) {
            if (isset($_POST['delete'])) {
                $blog_id = $_POST['blog_id'];
                $stmt = $this->conn->prepare("CALL delete_blog(?)");
                $stmt->bind_param("i", $blog_id);
                $result = $stmt->execute();
                if ($result) {
                    $this->returnToHome();
                } else {
                    $this->returnToHome();
                }
            } else {
                $query = "SELECT * FROM blogs";
                $result = $this->conn->query($query);
                ?>
                                <form action="/deleteBlog" method="POST">
                                    <label for="blog_id">Choose Blogs to Delete</label><br>
                                    <select name="blog_id">
                                        <?php while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . $row['id'] . "'>" . $row['topic'] . "</option>";
                                        } ?>
                                    </select><br>
                                    <input type="submit" name="delete" value="Go">
                                </form>
                                <?php
            }
        } else {
            $this->returnToHome();
        }
    }

    public function returnToHome()
    {
        header('Location: /');
        exit;
    }
}
$blog = new blog();
$blog->listen();

