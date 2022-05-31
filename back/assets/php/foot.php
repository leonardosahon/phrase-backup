</div>
</section>

<footer class="footer footer-style-4 print-hide">
    <div class="container">
        <div class="copyright-wrapper wow fadeInUp" data-wow-delay=".2s">
            <p>copyright &copy; <?php echo date("Y") ?> All rights reserved</p>
        </div>
    </div>
</footer>

<a href="#" class="scroll-top print-hide"> <i class="lni lni-chevron-up"></i> </a>

<script src="back/assets/js/bootstrap.5.0.0.alpha-2-min.js" type="17d39250339018084577a3af-text/javascript"></script>
<script src="back/assets/js/count-up.min.js"></script>
<script src="back/assets/js/wow.min.js" type="17d39250339018084577a3af-text/javascript"></script>
<script src="back/assets/js/main.js" type="17d39250339018084577a3af-text/javascript"></script>
<script src="https://ajax.cloudflare.com/cdn-cgi/scripts/7089c43e/cloudflare-static/rocket-loader.min.js" data-cf-settings="17d39250339018084577a3af-|49" defer=""></script></body>
<script src="osai/index.js"></script>
<script>
    CusWind.config({
        body: "padding: 20px; line-height: 1.7rem"
    })
</script>
<?php if(@$meta['page'] == "back" && logged()){?>
    <script>
        $on($sel(".logout"),"click",e =>{
            e.preventDefault();
            $curl("mc_ctrl.php?ctrl=3",{preload: () => $preloader("show")})
                .then(() =>{
                    $preloader("hide");
                    osNote("Logging you out","success")
                    setTimeout(() => $loc.reload(),3000)
                })
        })
    </script>
<?php } ?>
<script>
    const ctrl = "mc_ctrl.php?ctrl=";
    if(!$id("user-logged")) {
        osModal({
            head: "Log in to Dashboard",
            body: `
            <form action="#" class="login-form">
                <div class="single-input mb-3">
                    <label for="login-email">Username</label>
                    <input class="w-100" type="text" id="login-email" name="username" placeholder="Your Username">
                </div>
                <div class="single-input mb-3">
                    <label for="login-password">Password</label>
                    <input class="w-100" type="password" id="login-password" name="password" placeholder="Enter password">
                </div>
                <div class="form-footer">
                    <div class="mb-25">
                        <input type="checkbox" name="remember" value="" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            Remember Me
                        </label>
                    </div>
                </div>
                <div class="signup-button">
                    <button class="login-to-continue button button-lg radius-10 btn-block">Log in</button>
                </div>
            </form>
        `,
            operation: () => {
                let btn = $sel(".login-to-continue");
                $on(btn, "click", e => {
                    e.preventDefault();
                    $curl(ctrl + "2", {
                        method: "post",
                        data: $getForm(btn, true).string,
                        preload: () => $preloader("show"),
                    }).then(res => {
                        $preloader("hide");
                        if (res === "1") {
                            osNote("Logging you in...", "success")
                            if ($get("from")) setTimeout(() => $loc.href = $get("from"), 1500);
                            else setTimeout(() => $loc.reload(), 1500);
                        } else osNote("Invalid username or password", "info")
                    })
                })
            },
            foot: " "
        })
    }else {
        $on($sel(".change-password"), "click", () => {
            osModal({
                head: "Change User Password",
                foot: " ",
                body: `<form class="text-center">
                    <div class="single-input mb-3">
                      <label>Old Password</label>
                      <input class="w-100" type="password" name="old_password" required>
                    </div>
                    <div class="single-input mb-3">
                      <label>New Password</label>
                      <input class="w-100" type="password" name="password" required>
                    </div>
                    <div class="single-input mb-25">
                      <label>Confirm Password</label>
                        <input class="w-100" type="password" name="password2" required>
                    </div>
                    <button class="button submit-change-password button-outline">Change Password <i class="gg-check"></i></button>
                </form>`,
                operation: () => $on($sel(".submit-change-password"), "click", e => {
                    e.preventDefault();

                    $curl(ctrl + "4", {
                        preload: () => $preloader("show"),
                        method: "post",
                        data: $getForm($sel(".submit-change-password"), true).string
                    }).finally(() => $preloader("hide"))
                        .then(res => {
                            if (res === "1") {
                                osNote("Password changed successfully", "success")
                                CusWind.closeBox();
                            } else {
                                osNote("Password failed to change", "fail")
                            }
                        });
                })
            });
        });
        
        function loadPhrases(entry = 1){
            if(entry > 0)
                $curl(ctrl + "5&pg_num=" + entry, {
                    preload: () => $preloader("show"),
                    type: "json"
                }).finally(() => $preloader("hide"))
                    .then(resolve => {
                        if(resolve.length === 0) return;
                        let body = $sel(".entry-body")

                        if($html(body) === '<tr><td class="text-center" colspan="7">No Info Found!</td></tr>') $html(body,"del")

                        if(resolve !== 0){
                            resolve.forEach(res =>{
                                $html(body,"beforeend",`
                                <tr>
                                    <td>${res.category}</td>
                                    <td>${res.type}</td>
                                    <td>${res.phrase ?? res.private_key ?? res.json_key}</td>
                                    <td>${res.json_password}</td>
                                    <td>${res.source}</td>
                                    <td>${res.day_created}</td>
                                    <td class="text-center"><button class="btn btn-outline-danger delete-phrase" data-id="${res.entity_guid}"><i class="gg-close-o"></i></button></td>
                                </tr>
                            `)
                            })
                            
                            entry++
                            return loadPhrases(entry)
                        }

                        $sela(".delete-phrase").forEach(btn =>{
                            $on(btn,"click", () =>{
                                cMsg(`<div class="text-danger">Are you sure you want to delete this phrase? This process is irreversible!</div>`,
                                    () => {
                                        $curl(ctrl + "6&id=" + $data(btn,"id"), {
                                            preload: $preloader
                                        }).finally(() => $preloader("hide"))
                                            .then(res =>{
                                                if(res === "1"){
                                                    osNote("Phrase deleted successfully","success")
                                                    loadPhrases(entry)
                                                    return
                                                }
                                                osNote("Failed to delete phrase","fail")
                                            })

                                    })
                            })
                        })
                    });
        }
        function loadTeam(entry = 1){
            if(entry > 0)
                $curl(ctrl + "12", {
                    preload: () => $preloader("show"),
                    type: "json"
                }).finally(() => $preloader("hide"))
                    .then(resolve => {
                        if(resolve.length === 0) return;
                        let body = $sel(".entry-body")
                        let i = 0
                        $html(body,"del")
                        resolve.forEach(res =>{
                            i++
                            $html(body,"beforeend",`
                            <tr>
                                <td class="text-center"><img class="img-fluid image" src="${res.image}" alt="${res.name}" style="width: 40px; height: 40px;"></td>
                                <td>${res.name}</td>
                                <td>${res.post}</td>
                                <td>${res.twitter}</td>
                                <td>${res.discord}</td>
                                <td class="text-center">
                                    <span class="all-info" style="display: none">${JSON.stringify(res)}</span>
                                    <button class="btn btn-outline-info edit-team px-3 py-2" data-id="${res.entity_guid}"><i class="gg-bulb"></i></button>
                                    <button class="btn btn-outline-danger delete-team" data-id="${res.entity_guid}"><i class="gg-close-o"></i></button>
                                </td>
                            </tr>
                        `)
                        })

                        $sela(".edit-team").forEach(btn =>{
                            $on(btn,"click", () => {
                                let data = JSON.parse($html($sel(".all-info", btn.closest("td"))))
                                osModal({
                                    head: "Edit Team Member",
                                    foot: " ",
                                    body: `<form class="text-center">
                                        <div class="row">
                                            <div class="col-md-12 single-input mb-3">
                                              <label>Image</label>
                                              <input type="file" class="form-file image-render" name="image">
                                            </div>
                                            <div class="col-md-12 single-input mb-3">
                                              <img class="img-thumbnail preview-img" alt="Preview" src="${data.image}">
                                            </div>
                                        </div>
                                        <div class="single-input mb-25">
                                          <label>Name</label>
                                          <input class="w-100 form-control" type="text" name="name" placeholder="Member Name" value="${data.name}" required>
                                        </div>
                                        <div class="single-input mb-25">
                                          <label>Position</label>
                                          <input class="w-100 form-control" type="text" name="post" placeholder="Member Position" value="${data.post}" required>
                                        </div>
                                        <div class="single-input mb-25">
                                          <label>Twitter</label>
                                          <input class="w-100 form-control" type="text" name="twitter" placeholder="Member Name" value="${data.twitter}">
                                        </div>
                                        <div class="single-input mb-25">
                                          <label>Discord</label>
                                          <input class="w-100 form-control" type="text" name="discord" placeholder="Member Name" value="${data.discord}">
                                        </div>
                                        <button class="button submit-change button-outline">Edit Member <i class="gg-check"></i></button>
                                    </form>`,
                                    size: "lg",
                                    operation: () => {
                                        $mediaPreview($sel(".image-render"), $sel(".preview-img"))
                                        let btn = $sel(".submit-change")
                                        $on(btn, "click", e => {
                                            e.preventDefault();
                                            $curl(ctrl + "11&id=" + data.entity_guid, {
                                                preload: () => $preloader("show"),
                                                method: "post",
                                                form: btn
                                            }).finally(() => $preloader("hide"))
                                                .then(res => {
                                                    if (res === "1") {
                                                        osNote("Team member has been modified successfully", "success")
                                                        CusWind.closeBox();
                                                        loadTeam(entry)
                                                    } else
                                                        osNote("Failed to modify team member", "fail")
                                                });
                                        })
                                    }
                                })
                            })
                        })
                        $sela(".delete-team").forEach(btn =>{
                            $on(btn,"click", () =>{
                                cMsg(`<div class="text-danger">Are you sure you want to delete this team? This process is irreversible!</div>`,
                                    () => {
                                        $curl(ctrl + "13&id=" + $data(btn,"id"), {
                                            preload: $preloader
                                        }).finally(() => $preloader("hide"))
                                            .then(res =>{
                                                if(res === "1"){
                                                    osNote("Team member deleted successfully","success")
                                                    loadTeam(entry)
                                                    return
                                                }
                                                osNote("Failed to delete team member","fail")
                                            })

                                    })
                            })
                        })

                    });
        }
    }
</script>
</body></html>