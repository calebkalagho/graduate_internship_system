<?php
include('layout/front/header.php')
?>
<!-- Carousel Start -->
<div class="carousel-header">
    <div id="successCarousel" class="carousel slide" data-bs-ride="carousel">
        <ol class="carousel-indicators">
            <li data-bs-target="#successCarousel" data-bs-slide-to="0" class="active"></li>
            <li data-bs-target="#successCarousel" data-bs-slide-to="1"></li>
            <li data-bs-target="#successCarousel" data-bs-slide-to="2"></li>
        </ol>
        <div class="carousel-inner" role="listbox">
            <div class="carousel-item active">
                <img src="assets/front/img/success-1.jpg" class="img-fluid w-100" alt="Graduate Success Story">
                <div class="carousel-caption d-none d-md-block">
                    <h2 class="display-4 text-white mb-4 animated fadeInDown">Inspiring Success Stories</h2>
                    <p class="lead text-white mb-4 animated fadeInUp">Real graduates, real achievements, real impact in Malawi</p>
                    <a href="#" class="btn btn-primary btn-lg animated fadeInLeft">Read Stories</a>
                    <a href="#" class="btn btn-secondary btn-lg animated fadeInRight">Share Your Story</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/front/img/success-2.jpg" class="img-fluid w-100" alt="Career Transformation">
                <div class="carousel-caption d-none d-md-block">
                    <h2 class="display-4 text-white mb-4 animated fadeInDown">Career Transformation</h2>
                    <p class="lead text-white mb-4 animated fadeInUp">From classroom to boardroom - witness incredible journeys</p>
                    <a href="#" class="btn btn-primary btn-lg animated fadeInLeft">View Testimonials</a>
                    <a href="#" class="btn btn-secondary btn-lg animated fadeInRight">Alumni Network</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/front/img/success-3.jpg" class="img-fluid w-100" alt="Leadership Excellence">
                <div class="carousel-caption d-none d-md-block">
                    <h2 class="display-4 text-white mb-4 animated fadeInDown">Leadership Excellence</h2>
                    <p class="lead text-white mb-4 animated fadeInUp">Our graduates are leading change across industries</p>
                    <a href="#" class="btn btn-primary btn-lg animated fadeInLeft">Meet Leaders</a>
                    <a href="#" class="btn btn-secondary btn-lg animated fadeInRight">Join Program</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#successCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#successCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>
<!-- Carousel End -->

<!-- Modal Search Start -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h4 class="modal-title mb-0" id="exampleModalLabel">Search success stories</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex align-items-center">
                <div class="input-group w-75 mx-auto d-flex">
                    <input type="search" class="form-control p-3" placeholder="Search by name, company, or field" aria-describedby="search-icon-1">
                    <span id="search-icon-1" class="input-group-text btn border p-3"><i class="fa fa-search text-white"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Search End -->

<!-- Feature Start -->
<div class="container-fluid feature bg-light py-5">
    <div class="container py-5">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
            <h4 class="text-uppercase text-primary">Graduate Achievements</h4>
            <h1 class="display-3 text-capitalize mb-3">Celebrating Our Alumni Success</h1>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.2s">
                <div class="feature-item p-4">
                    <div class="feature-icon mb-3"><i class="fas fa-trophy text-white fa-3x"></i></div>
                    <a href="#" class="h4 mb-3">Award Winners</a>
                    <p class="mb-3">Our graduates have won numerous industry awards and recognitions for their outstanding contributions.</p>
                    <a href="#" class="btn text-secondary">View Awards <i class="fa fa-angle-right"></i></a>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.4s">
                <div class="feature-item p-4">
                    <div class="feature-icon mb-3"><i class="fas fa-rocket text-white fa-3x"></i></div>
                    <a href="#" class="h4 mb-3">Entrepreneurs</a>
                    <p class="mb-3">Many of our alumni have started successful businesses, contributing to Malawi's economic growth.</p>
                    <a href="#" class="btn text-secondary">Meet Founders <i class="fa fa-angle-right"></i></a>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.6s">
                <div class="feature-item p-4">
                    <div class="feature-icon mb-3"><i class="fas fa-globe text-white fa-3x"></i></div>
                    <a href="#" class="h4 mb-3">Global Impact</a>
                    <p class="mb-3">Our graduates are making a difference locally and internationally across various sectors.</p>
                    <a href="#" class="btn text-secondary">Global Stories <i class="fa fa-angle-right"></i></a>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.8s">
                <div class="feature-item p-4">
                    <div class="feature-icon mb-3"><i class="fas fa-heart text-white fa-3x"></i></div>
                    <a href="#" class="h4 mb-3">Community Leaders</a>
                    <p class="mb-3">Alumni are leading community development initiatives and social impact programs across Malawi.</p>
                    <a href="#" class="btn text-secondary">Social Impact <i class="fa fa-angle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Feature End -->

<!-- About Start -->
<div class="container-fluid about overflow-hidden py-5">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-xl-6 wow fadeInLeft" data-wow-delay="0.2s">
                <div class="about-img rounded h-100">
                    <img src="assets/front/img/success-featured.jpg" class="img-fluid rounded h-100 w-100" style="object-fit: cover;" alt="">
                    <div class="about-exp"><span>Inspiring Excellence</span></div>
                </div>
            </div>
            <div class="col-xl-6 wow fadeInRight" data-wow-delay="0.2s">
                <div class="about-item">
                    <h4 class="text-primary text-uppercase">Featured Success</h4>
                    <h1 class="display-3 mb-3">Chisomo Banda: From Intern to CEO</h1>
                    <p class="mb-4">Chisomo Banda's journey exemplifies the transformative power of the Malawi Graduate Internship Programs. Starting as an intern in 2018, she quickly demonstrated exceptional leadership skills and innovative thinking that set her apart from her peers.</p>
                    <div class="bg-light rounded p-4 mb-4">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex">
                                    <div class="pe-4">
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;"><i class="fas fa-star text-white fa-2x"></i></div>
                                    </div>
                                    <div class="">
                                        <a href="#" class="h4 d-inline-block mb-3">Outstanding Achievement</a>
                                        <p class="mb-0">Within 5 years, Chisomo rose to become CEO of a leading fintech company, revolutionizing mobile banking in Malawi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-light rounded p-4 mb-4">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex">
                                    <div class="pe-4">
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;"><i class="fas fa-lightbulb text-white fa-2x"></i></div>
                                    </div>
                                    <div class="">
                                        <a href="#" class="h4 d-inline-block mb-3">Innovation Impact</a>
                                        <p class="mb-0">Her innovative solutions have improved financial inclusion for over 200,000 Malawians in rural communities.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn btn-secondary rounded-pill py-3 px-5">Read Full Story</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- About End -->

<!-- Fact Counter -->
<div class="container-fluid counter py-5">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.2s">
                <div class="counter-item">
                    <div class="counter-item-icon mx-auto">
                        <i class="fas fa-medal fa-3x text-white"></i>
                    </div>
                    <h4 class="text-white my-4">Industry Awards</h4>
                    <div class="counter-counting">
                        <span class="text-white fs-2 fw-bold" data-toggle="counter-up">127</span>
                        <span class="h1 fw-bold text-white">+</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.4s">
                <div class="counter-item">
                    <div class="counter-item-icon mx-auto">
                        <i class="fas fa-user-tie fa-3x text-white"></i>
                    </div>
                    <h4 class="text-white my-4">Executive Positions</h4>
                    <div class="counter-counting">
                        <span class="text-white fs-2 fw-bold" data-toggle="counter-up">89</span>
                        <span class="h1 fw-bold text-white">+</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.6s">
                <div class="counter-item">
                    <div class="counter-item-icon mx-auto">
                        <i class="fas fa-store fa-3x text-white"></i>
                    </div>
                    <h4 class="text-white my-4">Businesses Started</h4>
                    <div class="counter-counting">
                        <span class="text-white fs-2 fw-bold" data-toggle="counter-up">156</span>
                        <span class="h1 fw-bold text-white">+</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.8s">
                <div class="counter-item">
                    <div class="counter-item-icon mx-auto">
                        <i class="fas fa-handshake fa-3x text-white"></i>
                    </div>
                    <h4 class="text-white my-4">Jobs Created</h4>
                    <div class="counter-counting">
                        <span class="text-white fs-2 fw-bold" data-toggle="counter-up">2840</span>
                        <span class="h1 fw-bold text-white">+</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fact Counter -->

<!-- Service Start -->
<div class="container-fluid service bg-light overflow-hidden py-5">
    <div class="container py-5">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
            <h4 class="text-uppercase text-primary">Success Categories</h4>
            <h1 class="display-3 text-capitalize mb-3">Diverse Paths to Excellence</h1>
        </div>
        <div class="row gx-0 gy-4 align-items-center">
            <div class="col-lg-6 col-xl-4 wow fadeInLeft" data-wow-delay="0.2s">
                <div class="service-item rounded p-4 mb-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex">
                                <div class="service-content text-end">
                                    <a href="#" class="h4 d-inline-block mb-3">Healthcare Heroes</a>
                                    <p class="mb-0">Medical graduates making breakthrough contributions to public health and healthcare delivery.</p>
                                </div>
                                <div class="ps-4">
                                    <div class="service-btn"><i class="fas fa-stethoscope text-white fa-2x"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="service-item rounded p-4 mb-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex">
                                <div class="service-content text-end">
                                    <a href="#" class="h4 d-inline-block mb-3">Tech Innovators</a>
                                    <p class="mb-0">Technology graduates creating digital solutions that transform industries and communities.</p>
                                </div>
                                <div class="ps-4">
                                    <div class="service-btn"><i class="fas fa-laptop-code text-white fa-2x"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="service-item rounded p-4 mb-0">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex">
                                <div class="service-content text-end">
                                    <a href="#" class="h4 d-inline-block mb-3">Education Leaders</a>
                                    <p class="mb-0">Education graduates revolutionizing learning and shaping the next generation of leaders.</p>
                                </div>
                                <div class="ps-4">
                                    <div class="service-btn"><i class="fas fa-chalkboard-teacher text-white fa-2x"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.3s">
                <div class="bg-transparent">
                    <img src="assets/front/img/success-stories.png" class="img-fluid w-100" alt="">
                </div>
            </div>
            <div class="col-lg-6 col-xl-4 wow fadeInRight" data-wow-delay="0.2s">
                <div class="service-item rounded p-4 mb-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex">
                                <div class="pe-4">
                                    <div class="service-btn"><i class="fas fa-seedling text-white fa-2x"></i></div>
                                </div>
                                <div class="service-content">
                                    <a href="#" class="h4 d-inline-block mb-3">Agriculture Champions</a>
                                    <p class="mb-0">Agricultural graduates pioneering sustainable farming practices and food security initiatives.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="service-item rounded p-4 mb-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex">
                                <div class="pe-4">
                                    <div class="service-btn"><i class="fas fa-balance-scale text-white fa-2x"></i></div>
                                </div>
                                <div class="service-content">
                                    <a href="#" class="h4 d-inline-block mb-3">Legal Advocates</a>
                                    <p class="mb-0">Law graduates championing justice and human rights while strengthening legal institutions.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="service-item rounded p-4 mb-0">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex">
                                <div class="pe-4">
                                    <div class="service-btn"><i class="fas fa-coins text-white fa-2x"></i></div>
                                </div>
                                <div class="service-content">
                                    <a href="#" class="h4 d-inline-block mb-3">Financial Pioneers</a>
                                    <p class="mb-0">Business graduates driving economic growth and financial inclusion across Malawi.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Service End -->
<?php
include('layout/front/footer.php')
?>