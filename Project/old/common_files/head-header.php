<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/project.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/project.js"></script>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="main.php">OSP eStore</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#expanded-content">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="expanded-content">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="main.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="services.php">Types of Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reviews.php">Reviews</a>
                        </li>
                    </ul>
                    <hr>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">Shopping Cart</a>
                        </li>
                        <?php
                            if(isset($_COOKIE['logged-type']) && $_COOKIE['logged-type'] == 'admin') {
                                echo <<<USERITEM
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown">DB Maintain</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="db-maintain-insert.php">Insert</a></li>
                                        <li><a class="dropdown-item" href="db-maintain-delete.php">Delete</a></li>
                                        <li><a class="dropdown-item" href="db-maintain-select.php">Select</a></li>
                                        <li><a class="dropdown-item" href="db-maintain-update.php">Update</a></li>
                                    </ul>
                                </li>
                                USERITEM;
                            }
                        ?>
                        <?php
                            if(isset($_COOKIE['logged-name'])) {
                                $name = $_COOKIE['logged-name'];
                                echo <<<USERITEM
                                <li class='nav-item dropdown'>
                                    <a class='nav-link dropdown-toggle' role='button' data-bs-toggle='dropdown'>$name</a>
                                    <ul class='dropdown-menu dropdown-menu-end'>
                                        <li><a class='dropdown-item' id='signout'>Sign Out</a></li>
                                    </ul>
                                </li>
                                USERITEM;
                            } else {
                                echo <<<USERITEM
                                <li class='nav-item dropdown'>
                                    <a class='nav-link dropdown-toggle' role='button' data-bs-toggle='dropdown'>Sign In/Up</a>
                                    <ul class='dropdown-menu dropdown-menu-end'>
                                        <li><a class='dropdown-item' href='sign-in.php'>Sign In</a></li>
                                        <li><a class='dropdown-item' href='sign-up.php'>Sign Up</a></li>
                                    </ul>
                                </li>
                                USERITEM;
                            }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</body>