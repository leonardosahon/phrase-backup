<?php
include_once "back/assets/php/xxx.inc";

$meta['title'] = "Add New User";
$meta['position-relative'] = 1;
$meta['page'] = "back";
include_once "back/assets/php/head.php";
?>

<section class="signup signup-style-1 mt-0 pt-40">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="signup-content-wrapper">
                    <div class="section-title">
                        <h3 class="mb-20">Sign Up</h3>
                        <p>Add a new user</p>
                    </div>
                    <div class="image">
                        <img src="back/assets/img/about/about-4/about-img.svg" alt="" class="w-100">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="signup-form-wrapper">
                    <form action="#" class="signup-form">
                        <div class="single-input">
                            <label for="signup-name">Name</label>
                            <input type="text" name="name" placeholder="Your Name">
                        </div>
                        <div class="single-input">
                            <label for="signup-uname">Username</label>
                            <input type="text" id="signup-uname" name="username" placeholder="Your Username">
                        </div>
                        <div class="single-input">
                            <label for="signup-email">Email</label>
                            <input type="email" id="signup-email" name="email" placeholder="Your Email">
                        </div>
                        <div class="single-input">
                            <label for="signup-password">Password</label>
                            <input type="password" id="signup-password" name="password" placeholder="Choose password">
                        </div>
                        <div class="single-input">
                            <label for="signup-password2">Confirm Password</label>
                            <input type="password" id="signup-password2" name="password2" placeholder="Repeat password">
                        </div>
                        <div class="signup-button mb-25">
                            <button class="send-button button button-lg radius-10 btn-block">Sign up</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once "back/assets/php/foot.php"?>
<script>
    let btn = $sel(".send-button");
    $on(btn,"click", e =>{
        e.preventDefault();
        $curl("mc_ctrl.php?ctrl=1", {
            method: "post",
            data: $getForm(btn,true).string,
            preload: () => $preloader("show"),
        }) .then(res => {
            $preloader("hide");
            if(res === "1") {
                osNote("User created successfully","success")
                btn.closest("form").reset();
            }
            else if(res === "2") osNote("User credentials exists already","info")
            else osNote("An error occurred, please try again later","fail")
        })
    })
</script>