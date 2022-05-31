<!DOCTYPE html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title><?php echo $meta['title'] ?> :: MyWalletFix</title>
    <meta content="A crypto wallet &amp; gateway to blockchain apps" name="description">
    <meta content="MetaMask" property="og:title">
    <meta content="A crypto wallet &amp; gateway to blockchain apps" property="og:description">
    <meta content="assets/favicon.png" property="og:image">
    <meta property="og:type" content="website">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta content="Webflow" name="generator">

    <link rel="stylesheet" href="back/assets/css/bootstrap-5.0.0-alpha-2.min.css" />
    <link rel="stylesheet" href="back/assets/css/LineIcons.2.0.css" />
    <link rel="stylesheet" href="back/assets/css/tiny-slider.css" />
    <link rel="stylesheet" href="back/assets/css/animate.css" />
    <link rel="stylesheet" href="back/assets/css/lindy-uikit.css" />
    <link href="back/assets/img/my-logo.png" rel="shortcut icon" type="image/x-icon">
    <link href="back/assets/img/my-logo.png" rel="apple-touch-icon">
</head>
<body>
<!--[if lte IE 9]>
<p class="browserupgrade">
    You are using an <strong>outdated</strong> browser. Please
    <a href="https://browsehappy.com/">upgrade your browser</a> to improve
    your experience and security.
</p>
<![endif]-->

<div class="preloader">
    <div class="loader">
        <div class="spinner">
            <div class="spinner-container">
                <div class="spinner-rotator">
                    <div class="spinner-left">
                        <div class="spinner-circle"></div>
                    </div>
                    <div class="spinner-right">
                        <div class="spinner-circle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<section id="home" class="hero-section-wrapper-5 print-hide">

    <header class="header header-6<?php if(isset($meta['position-relative'])) echo " position-relative" ?>">
        <div class="navbar-area">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg justify-content-center" style="padding-left: 10px; padding-right: 10px; background: rgba(255,255,255,.5); border-radius: 0 0 10px 10px;">
                            <a class="navbar-brand" href="/">
                                <img src="back/assets/img/my-logo.png" alt="Logo" />
                            </a>
                        </nav>

                    </div>
                </div>

            </div>

        </div>

    </header>

    <?php echo @$meta['hero']  ?>
</section>

<?php if(@$meta['user'] == "show" && logged()) { ?>
<input type='hidden' id='user-logged' value='<?php echo $_SESSION['active_user'] ?>'>
<details class="active-user bg-dark text-light" style="position: fixed;bottom: 0;padding: 10px;z-index: 10;border-radius: 10px 10px 0 0;">
    <summary>User Panel</summary>
    <div>
        Username: <span class="font-weight-bold"><?php echo $_SESSION['active_user_name'] ?></span>
    </div>
    <div>
        Name: <span class="font-weight-bold"><?php echo $_SESSION['active_user_full_name'] ?></span>
    </div>
    <div>
        <a href="#" class="logout">Logout</a>
    </div>
</details>
<?php } ?>
