<?php
include('layout/front/header.php')
?>
<!-- Carousel Start -->
<div class="carousel-header">
    <div id="internshipCarousel" class="carousel slide" data-bs-ride="carousel">
        <ol class="carousel-indicators">
            <li data-bs-target="#internshipCarousel" data-bs-slide-to="0" class="active"></li>
            <li data-bs-target="#internshipCarousel" data-bs-slide-to="1"></li>
            <li data-bs-target="#internshipCarousel" data-bs-slide-to="2"></li>
        </ol>
        <div class="carousel-inner" role="listbox">
            <div class="carousel-item active">
                <img src="assets/front/img/carousel-1.jpg" class="img-fluid w-100" alt="Malawi Internship Program">
                <div class="carousel-caption d-none d-md-block">
                    <h2 class="display-4 text-white mb-4 animated fadeInDown">Malawi Graduate Internships</h2>
                    <p class="lead text-white mb-4 animated fadeInUp">Empowering graduates with hands-on experience for a brighter future</p>
                    <a href="#" class="btn btn-primary btn-lg animated fadeInLeft">Apply Now</a>
                    <a href="#" class="btn btn-secondary btn-lg animated fadeInRight">Learn More</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/front/img/carousel-2.jpg" class="img-fluid w-100" alt="Skills Development">
                <div class="carousel-caption d-none d-md-block">
                    <h2 class="display-4 text-white mb-4 animated fadeInDown">Skills Development</h2>
                    <p class="lead text-white mb-4 animated fadeInUp">Gain practical experience in your field with our comprehensive programs</p>
                    <a href="#" class="btn btn-primary btn-lg animated fadeInLeft">Available Programs</a>
                    <a href="#" class="btn btn-secondary btn-lg animated fadeInRight">Partner Companies</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/front/img/carousel-3.jpg" class="img-fluid w-100" alt="Career Opportunities">
                <div class="carousel-caption d-none d-md-block">
                    <h2 class="display-4 text-white mb-4 animated fadeInDown">Career Opportunities</h2>
                    <p class="lead text-white mb-4 animated fadeInUp">Connect with top employers and jumpstart your career in Malawi</p>
                    <a href="#" class="btn btn-primary btn-lg animated fadeInLeft">Success Stories</a>
                    <a href="#" class="btn btn-secondary btn-lg animated fadeInRight">Job Board</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#internshipCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#internshipCarousel" data-bs-slide="next">
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
                <h4 class="modal-title mb-0" id="exampleModalLabel">Search by keyword</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex align-items-center">
                <div class="input-group w-75 mx-auto d-flex">
                    <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
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
            <h4 class="text-uppercase text-primary">Our Features</h4>
            <h1 class="display-3 text-capitalize mb-3">Empowering Malawi's Future Leaders</h1>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.2s">
                <div class="feature-item p-4">
                    <div class="feature-icon mb-3"><i class="fas fa-graduation-cap text-white fa-3x"></i></div>
                    <a href="#" class="h4 mb-3">Skill Development</a>
                    <p class="mb-3">Enhance your professional skills through hands-on experience and targeted training programs.</p>
                    <a href="#" class="btn text-secondary">Learn More <i class="fa fa-angle-right"></i></a>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.4s">
                <div class="feature-item p-4">
                    <div class="feature-icon mb-3"><i class="fas fa-users text-white fa-3x"></i></div>
                    <a href="#" class="h4 mb-3">Mentorship</a>
                    <p class="mb-3">Benefit from guidance and support from experienced professionals in your field of study.</p>
                    <a href="#" class="btn text-secondary">Learn More <i class="fa fa-angle-right"></i></a>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.6s">
                <div class="feature-item p-4">
                    <div class="feature-icon mb-3"><i class="fas fa-briefcase text-white fa-3x"></i></div>
                    <a href="#" class="h4 mb-3">Industry Exposure</a>
                    <p class="mb-3">Gain valuable insights into various industries and build a strong professional network.</p>
                    <a href="#" class="btn text-secondary">Learn More <i class="fa fa-angle-right"></i></a>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.8s">
                <div class="feature-item p-4">
                    <div class="feature-icon mb-3"><i class="fas fa-chart-line text-white fa-3x"></i></div>
                    <a href="#" class="h4 mb-3">Career Growth</a>
                    <p class="mb-3">Kickstart your career with opportunities for full-time employment and continued professional development.</p>
                    <a href="#" class="btn text-secondary">Learn More <i class="fa fa-angle-right"></i></a>
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
                    <img src="assets/front/img/about.jpg" class="img-fluid rounded h-100 w-100" style="object-fit: cover;" alt="">
                    <div class="about-exp"><span>Empowering Graduates</span></div>
                </div>
            </div>
            <div class="col-xl-6 wow fadeInRight" data-wow-delay="0.2s">
                <div class="about-item">
                    <h4 class="text-primary text-uppercase">About Us</h4>
                    <h1 class="display-3 mb-3">Malawi Graduate Internship Programs</h1>
                    <p class="mb-4">The Malawi Graduate Internship Programs aim to provide fresh graduates with valuable work experience, bridging the gap between academic learning and professional employment. These programs are designed to empower graduates by equipping them with the necessary skills to excel in their chosen fields.</p>
                    <div class="bg-light rounded p-4 mb-4">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex">
                                    <div class="pe-4">
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;"><i class="fas fa-briefcase text-white fa-2x"></i></div>
                                    </div>
                                    <div class="">
                                        <a href="#" class="h4 d-inline-block mb-3">Real-World Experience</a>
                                        <p class="mb-0">Gain hands-on experience by working in leading industries and organizations across Malawi.</p>
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
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;"><i class="fas fa-graduation-cap text-white fa-2x"></i></div>
                                    </div>
                                    <div class="">
                                        <a href="#" class="h4 d-inline-block mb-3">Professional Growth</a>
                                        <p class="mb-0">Our programs foster personal and professional growth, preparing graduates for the competitive job market.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn btn-secondary rounded-pill py-3 px-5">Read More</a>
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
                        <i class="fas fa-thumbs-up fa-3x text-white"></i>
                    </div>
                    <h4 class="text-white my-4">Successful Interns</h4>
                    <div class="counter-counting">
                        <span class="text-white fs-2 fw-bold" data-toggle="counter-up">456</span>
                        <span class="h1 fw-bold text-white">+</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.4s">
                <div class="counter-item">
                    <div class="counter-item-icon mx-auto">
                        <i class="fas fa-building fa-3x text-white"></i>
                    </div>
                    <h4 class="text-white my-4">Partner Organizations</h4>
                    <div class="counter-counting">
                        <span class="text-white fs-2 fw-bold" data-toggle="counter-up">513</span>
                        <span class="h1 fw-bold text-white">+</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.6s">
                <div class="counter-item">
                    <div class="counter-item-icon mx-auto">
                        <i class="fas fa-users fa-3x text-white"></i>
                    </div>
                    <h4 class="text-white my-4">Active Interns</h4>
                    <div class="counter-counting">
                        <span class="text-white fs-2 fw-bold" data-toggle="counter-up">53</span>
                        <span class="h1 fw-bold text-white">+</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.8s">
                <div class="counter-item">
                    <div class="counter-item-icon mx-auto">
                        <i class="fas fa-calendar-alt fa-3x text-white"></i>
                    </div>
                    <h4 class="text-white my-4">Years of Impact</h4>
                    <div class="counter-counting">
                        <span class="text-white fs-2 fw-bold" data-toggle="counter-up">17</span>
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
            <h4 class="text-uppercase text-primary">Our Services</h4>
            <h1 class="display-3 text-capitalize mb-3">Empowering Graduates for Future Success</h1>
        </div>
        <div class="row gx-0 gy-4 align-items-center">
            <div class="col-lg-6 col-xl-4 wow fadeInLeft" data-wow-delay="0.2s">
                <div class="service-item rounded p-4 mb-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex">
                                <div class="service-content text-end">
                                    <a href="#" class="h4 d-inline-block mb-3">Graduate Training</a>
                                    <p class="mb-0">We provide intensive training to equip graduates with practical skills for the job market.</p>
                                </div>
                                <div class="ps-4">
                                    <div class="service-btn"><i class="fas fa-chalkboard-teacher text-white fa-2x"></i></div>
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
                                    <a href="#" class="h4 d-inline-block mb-3">Mentorship Programs</a>
                                    <p class="mb-0">Connect with experienced professionals for guidance and support in career development.</p>
                                </div>
                                <div class="ps-4">
                                    <div class="service-btn"><i class="fas fa-user-tie text-white fa-2x"></i></div>
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
                                    <a href="#" class="h4 d-inline-block mb-3">Job Placements</a>
                                    <p class="mb-0">Facilitating job placements with reputable companies across various sectors.</p>
                                </div>
                                <div class="ps-4">
                                    <div class="service-btn"><i class="fas fa-briefcase text-white fa-2x"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.3s">
                <div class="bg-transparent">
                    <img src="assets/front/img/internship.png" class="img-fluid w-100" alt="">
                </div>
            </div>
            <div class="col-lg-6 col-xl-4 wow fadeInRight" data-wow-delay="0.2s">
                <div class="service-item rounded p-4 mb-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex">
                                <div class="pe-4">
                                    <div class="service-btn"><i class="fas fa-network-wired text-white fa-2x"></i></div>
                                </div>
                                <div class="service-content">
                                    <a href="#" class="h4 d-inline-block mb-3">Networking Opportunities</a>
                                    <p class="mb-0">Build strong networks with industry leaders and fellow interns for future growth.</p>
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
                                    <div class="service-btn"><i class="fas fa-chart-line text-white fa-2x"></i></div>
                                </div>
                                <div class="service-content">
                                    <a href="#" class="h4 d-inline-block mb-3">Career Growth</a>
                                    <p class="mb-0">Prepare for long-term career growth with personalized coaching and skill development.</p>
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
                                    <div class="service-btn"><i class="fas fa-tasks text-white fa-2x"></i></div>
                                </div>
                                <div class="service-content">
                                    <a href="#" class="h4 d-inline-block mb-3">Project Management</a>
                                    <p class="mb-0">Gain hands-on experience in project management to boost leadership and team coordination skills.</p>
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