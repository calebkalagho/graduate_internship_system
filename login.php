<?php include('layout/front/header.php'); ?>

<!-- Contact Start -->
<div class="container-fluid contact bg-light py-5">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-12 h-100">
                <div class="text-center mx-auto pb-5" style="max-width: 800px;">
                    <h4 class="text-uppercase text-primary">Sign In</h4>
                </div>
                <form action="./endpoint/login.php" method="POST">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger rounded-0">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success rounded-0">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    <div class="row g-4">
                        <div class="col-lg-12 col-xl-12">
                            <div class="form-floating">
                                <input type="text" class="form-control border-0" id="username" name="username" placeholder="Your email">
                                <label for="username">Email</label>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-12">
                            <div class="form-floating">
                                <input type="password" class="form-control border-0" id="password" name="password" placeholder="Your Password">
                                <label for="password">Password</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary w-100 py-3">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Contact End -->

<?php include('layout/front/footer.php'); ?>