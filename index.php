<?php
/*
Copyright 2017 iamthemanintheshower@gmail.com

Permission is hereby granted, free of charge, to any person obtaining a copy of 
this software and associated documentation files (the "Software"), to deal in 
the Software without restriction, including without limitation the rights to use, 
copy, modify, merge, publish, distribute, sublicense, and/or sell copies 
of the Software, and to permit persons to whom the Software is furnished 
to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in 
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
PARTICULAR PURPOSE AND NONINFRINGEMENT. 
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, 
DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER 
DEALINGS IN THE SOFTWARE.
*/

include 'conf/app_config.php';
include 'conf/config.php';
$status = '';

if(isset($_POST) 
    && isset($_POST['website-url']) && $_POST['website-url'] !== ''
    && isset($_POST['wp_title']) && $_POST['wp_title'] !== ''
    ){
    $wp_name = $db_name = $_POST['website-url'];
    $wp_title = $_POST['wp_title'];

    $wp_config_tmpl_content = file_get_contents($wp_config_tmpl_filename);
    $WP_db_content = file_get_contents($WP_db_template);
    $htaccess_tmpl_content = file_get_contents($htaccess_tmpl_filename);

    //create the folder's project
    mkdir($WPes_root_path.$wp_name);

    //DB
    $conn = new mysqli($db_host, $db_user, $db_psw);
    if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 
    //TODO: create db user, not only the DB
    $sql = "CREATE DATABASE `$wp_name`";
    if ($conn->query($sql) === TRUE) { $error_message = "Database created successfully"; } else { echo "<pre>Error creating database: " . $conn->error.'</pre>'; die(); }

    //wp-config.php
    $wp_config = str_replace('#DB-NAME#', $db_name, $wp_config_tmpl_content);
    $wp_config = str_replace('#DB-USER#', $db_user, $wp_config);
    $wp_config = str_replace('#DB-PSW#', $db_psw, $wp_config);
    $wp_config = str_replace('#DB-HOST#', $db_host, $wp_config);
    file_put_contents($WPes_root_path.'/'.$wp_name.'/wp-config.php', $wp_config);

    //use an already customized .htaccess
    $htaccess = str_replace('#SITE-NAME#', $wp_base_url_no_domain.$wp_name, $htaccess_tmpl_content);
    file_put_contents($WPes_root_path.'/'.$wp_name.'/.htaccess', $htaccess);

    //use the WP instance from template
    recurse_copy('_template-hsdk/WP-template/',$WPes_root_path.'/'.$wp_name.'/');

    //customize the DB from a template
    $WP_db = str_replace('#SITE-URL#', $wp_base_url.$wp_name, $WP_db_content);
    $WP_db = str_replace('#SITE-NAME#', $wp_name, $WP_db);
    mysqli_select_db ( $conn, $db_name );

    //create the customized DB
    import_db($WP_db, $conn);
    $conn->close();
    $status = 'all-right';
}
?><!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo $app_title.' - '.$app_subtitle; ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Theme CSS -->
    <link href="css/clean-blog.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-custom navbar-fixed-top">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    Menu <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="index.html">WP Installer - with custom options</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a target="_blank" href="<?php echo $app_credits_url; ?>">Credits</a>
                    </li>
                    <li>
                        <a target="_blank" href="<?php echo $app_license_url; ?>">License</a>
                    </li>
                    <li>
                        <a target="_blank" href="<?php echo $app_support_url; ?>">Support</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Header -->
    <!-- Set your background image for this header on the line below. -->
    <header class="intro-header" style="background-image: url('img/home-bg.jpg')">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    <div class="site-heading">
                        <h1>WP Installer</h1>
                        <hr class="small">
                        <span class="subheading">...with custom options</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <?php
                if($status === 'all-right'){?>
                    <br>
                    <br>
                    <a target="_blank" href="<?php echo $wp_base_url.$wp_name; ?>">WEBSITE</a><br>
                    <a target="_blank" href="<?php echo $wp_base_url.$wp_name; ?>/wp-login-ioassduw.php">LOGIN</a><br>
                    admin-wiueksd<br>
                    R5UKBt0pNw<br>
                    <br>
                    <a href="index.php">Refresh this page</a>
                <?php
                }else{ 
                    if(isset($error_message) && $error_message !== ''){
                        echo $error_message;
                    }
                    ?>
                    <form class="form-horizontal" action="" method="post">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="website-url">Website name (this field is used for the URL and DB name)</label>
                            <div class="col-md-4">
                                <input id="website-url" name="website-url" type="text" placeholder="placeholder" class="form-control input-md">
                                <span class="help-block">Folder's name</span>  
                            </div>

                            <label class="col-md-4 control-label" for="wp_title">Website tile</label>
                            <div class="col-md-4">
                                <input id="wp_title" name="wp_title" type="text" placeholder="placeholder" class="form-control input-md">
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">Install your customized WP</button>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    </form>

                    <div class="row">
                        <div class="col-md-12">
                            <p>
                                <b>Customize the installer:</b><br>
                                – WP template folder: _template-hsdk<br>
                                – Options in conf/config.php<br>
                            </p>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <p>
                                <b>WP Template:</b><br>
                                – put your favorite WP instance under _template-hsdk/WP-template folder<br>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <p>
                                <b>Permalink customization:</b><br>
                                – .htaccess [site-url]/sample-post/
                                – permalink in DB [site-url]/sample-post/
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <p>
                                <b>wp-config.php customization:</b><br>
                                /** The name of the database for WP */<br>
                                define('DB_NAME', '#DB-NAME#');<br>
                                /** MySQL database username */<br>
                                define('DB_USER', '#DB-USER#');<br>
                                /** MySQL database password */<br>
                                define('DB_PASSWORD', '#DB-PSW#');<br>
                                /** MySQL hostname */<br>
                                define('DB_HOST', '#DB-HOST#');
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <p><b>Security:</b><br>
                                - renamed wp-login.php in wp-login-ioassduw.php<br>
                            </p>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <hr>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    <ul class="list-inline text-center">
                        <li>
                            <a target="_blank" href="<?php echo $app_twitter_url;?>">
                                <span class="fa-stack fa-lg">
                                    <i class="fa fa-circle fa-stack-2x"></i>
                                    <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="<?php echo $app_facebook_url;?>">
                                <span class="fa-stack fa-lg">
                                    <i class="fa fa-circle fa-stack-2x"></i>
                                    <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="<?php echo $app_github_url;?>">
                                <span class="fa-stack fa-lg">
                                    <i class="fa fa-circle fa-stack-2x"></i>
                                    <i class="fa fa-github fa-stack-1x fa-inverse"></i>
                                </span>
                            </a>
                        </li>
                    </ul>
                    <p class="copyright text-muted"><?php echo $app_copyright; ?></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

</body>

</html><?php
function recurse_copy($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
}
function import_db($lines, $conn){
    $templine = '';
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $lines) as $line){ 
        $templine .= $line;
        if (substr(trim($line), -1, 1) == ';') {
         mysqli_query($conn, $templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysqli_error($conn) . '<br /><br />');
         $templine = '';
        }
   }

}