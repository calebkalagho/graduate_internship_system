<?php include('layout/front/header.php'); ?>
<!-- Sign-Up Form Start -->
<div class="container-fluid contact bg-light py-5">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-12 h-100 wow fadeInUp" data-wow-delay="0.2s">
                <div class="text-center mx-auto pb-5" style="max-width: 800px;">
                    <h4 class="text-uppercase text-primary">Sign up</h4>
                </div>
                <form action="./endpoint/applicants.php" method="POST">
                    <div class="row g-4">
                        <!-- First Name -->
                        <div class="col-lg-12 col-xl-4">
                            <div class="form-floating">
                                <input type="text" class="form-control border-0" id="first_name" name="first_name" placeholder="First Name" required>
                                <label for="first_name">First Name</label>
                            </div>
                        </div>

                        <!-- Middle Name -->
                        <div class="col-lg-12 col-xl-4">
                            <div class="form-floating">
                                <input type="text" class="form-control border-0" id="middle_name" name="middle_name" placeholder="Middle Name">
                                <label for="middle_name">Middle Name</label>
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div class="col-lg-12 col-xl-4">
                            <div class="form-floating">
                                <input type="text" class="form-control border-0" id="last_name" name="last_name" placeholder="Last Name" required>
                                <label for="last_name">Last Name</label>
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="col-lg-12 col-xl-6">
                            <div class="form-floating">
                                <select class="form-control border-0" id="gender" name="gender" required>
                                    <option value="" disabled selected>Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                <label for="gender">Gender</label>
                            </div>
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-lg-12 col-xl-6">
                            <div class="form-floating">
                                <input type="date" class="form-control border-0" id="dob" name="dob" placeholder="Date of Birth" required>
                                <label for="dob">Date of Birth</label>
                            </div>
                        </div>

                        <!-- National ID -->
                        <div class="col-lg-12 col-xl-6">
                            <div class="form-floating">
                                <input type="text" class="form-control border-0" id="national_id" name="national_id" placeholder="National ID" required>
                                <label for="national_id">National ID</label>
                            </div>
                        </div>

                        <!-- Mobile Number -->
                        <div class="col-lg-12 col-xl-6">
                            <div class="form-floating">
                                <input type="tel" class="form-control border-0" id="mobile" name="mobile" placeholder="Mobile Number" required>
                                <label for="mobile">Mobile Number</label>
                                <small id="mobile_error" class="text-danger"></small>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-lg-12 col-xl-6">
                            <div class="form-floating">
                                <input type="email" class="form-control border-0" id="email" name="email" placeholder="Your Email" required>
                                <label for="email">Your Email</label>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="col-lg-12 col-xl-6">
                            <div class="form-floating">
                                <input type="password" class="form-control border-0" id="password" name="password" placeholder="Password" required>
                                <label for="password">Password</label>
                                <small id="password_error" class="text-danger"></small>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-lg-12 col-xl-6">
                            <div class="form-floating">
                                <input type="password" class="form-control border-0" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                                <label for="confirm_password">Confirm Password</label>
                                <small id="confirm_password_error" class="text-danger"></small>
                            </div>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary w-100 py-3" type="submit" name="add_applicant">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Sign-Up Form End -->

<?php include('layout/front/footer.php'); ?>