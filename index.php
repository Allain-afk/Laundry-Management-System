<?php
require_once 'includes/db_connect.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DRYME</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/card-hover.css" rel="stylesheet">
    <style>
        .btn:hover .order-text {
            color: #ffffff !important;
            transition: color 0.3s ease;
        }
    </style>
</head>

<body>
    <!-- Topbar Start -->
    <div class="container-fluid bg-primary py-3">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-lg-left mb-2 mb-lg-0">
                    <div class="d-inline-flex align-items-center">
                        <a class="text-white pr-3" href="">FAQs</a>
                        <span class="text-white">|</span>
                        <a class="text-white px-3" href="">Help</a>
                        <span class="text-white">|</span>
                        <a class="text-white pl-3" href="">Support</a>
                    </div>
                </div>
                <div class="col-md-6 text-center text-lg-right">
                    <div class="d-inline-flex align-items-center">
                        <a class="text-white px-3" href="">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a class="text-white px-3" href="">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a class="text-white px-3" href="">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a class="text-white px-3" href="">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a class="text-white pl-3" href="">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->


    <!-- Navbar Start -->
    <div class="container-fluid position-relative nav-bar p-0">
        <div class="container-lg position-relative p-0 px-lg-3" style="z-index: 9;">
            <nav class="navbar navbar-expand-lg bg-white navbar-light py-3 py-lg-0 pl-3 pl-lg-5">
                <a href="" class="navbar-brand">
                    <h1 class="m-0 text-secondary"><span class="text-primary">DRY</span>ME</h1>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between px-3" id="navbarCollapse">
                    <div class="navbar-nav ml-auto py-0">
                        <a href="index.php" class="nav-item nav-link active">Home</a>
                        <a href="about.php" class="nav-item nav-link">About</a>
                        <a href="service.php" class="nav-item nav-link">Services</a>
                        <a href="pricing.php" class="nav-item nav-link">Pricing</a>
                        <a href="contact.php" class="nav-item nav-link">Contact</a>

                        <!-- Add User Profile Dropdown -->
                        <?php
                        if (isset($_SESSION['user_id'])) { ?>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-user"></i> <?php echo $_SESSION['username']; ?>
                                </a>
                                <div class="dropdown-menu border-0 rounded-0 m-0">
                                    <div class="px-3 py-2">
                                        <p class="mb-1"><strong>Username:</strong> <?php echo $_SESSION['username']; ?></p>
                                        <p class="mb-1"><strong>Email:</strong> <?php echo $_SESSION['email']; ?></p>
                                        <?php if (isset($_SESSION['full_name'])): ?>
                                            <p class="mb-1"><strong>Full Name:</strong> <?php echo $_SESSION['full_name']; ?></p>
                                        <?php endif; ?>
                                        <?php if (isset($_SESSION['phone'])): ?>
                                            <p class="mb-1"><strong>Phone:</strong> <?php echo $_SESSION['phone']; ?></p>
                                        <?php endif; ?>
                                        <?php if (isset($_SESSION['address'])): ?>
                                            <p class="mb-1"><strong>Address:</strong> <?php echo $_SESSION['address']; ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a href="profile.php" class="dropdown-item">My Profile</a>
                                    <a href="helpers/logout.php" class="dropdown-item">Logout</a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <a href="login.php" class="nav-item nav-link">Login</a>
                        <?php } ?>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->


    <!-- Carousel Start -->
    <div class="container-fluid p-0">
        <div id="header-carousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="img/carousel-2.jpg" alt="Image">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h4 class="text-white text-uppercase mb-md-3">Laundry & Dry Cleaning</h4>
                            <h1 class="display-3 text-white mb-md-4">"Where your laundry gets the five-star treatment."</h1>
                            <a href="about.php" class="btn btn-primary py-md-3 px-md-5 mt-2">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="w-100" src="img/carousel-1.jpg" alt="Image">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h4 class="text-white text-uppercase mb-md-3">Free Pick Up & Delivery</h4>
                            <h1 class="display-3 text-white mb-md-4">"Because fresh, fast, and flawless is how laundry should be."</h1>
                            <a href="abput.php" class="btn btn-primary py-md-3 px-md-5 mt-2">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#header-carousel" data-slide="prev">
                <div class="btn btn-secondary" style="width: 45px; height: 45px;">
                    <span class="carousel-control-prev-icon mb-n2"></span>
                </div>
            </a>
            <a class="carousel-control-next" href="#header-carousel" data-slide="next">
                <div class="btn btn-secondary" style="width: 45px; height: 45px;">
                    <span class="carousel-control-next-icon mb-n2"></span>
                </div>
            </a>
        </div>
    </div>
    <!-- Carousel End -->


    <!-- Contact Info Start -->
    <div class="container-fluid contact-info mt-5 mb-4">
        <div class="container" style="padding: 0 30px;">
            <div class="row">
                <div class="col-md-4 d-flex align-items-center justify-content-center bg-secondary mb-4 mb-lg-0" style="height: 100px;">
                    <div class="d-inline-flex">
                        <i class="fa fa-2x fa-map-marker-alt text-white m-0 mr-3"></i>
                        <div class="d-flex flex-column">
                            <h5 class="text-white font-weight-medium">Our Location</h5>
                            <p class="m-0 text-white">Cebu City, Philippines</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-center justify-content-center bg-primary mb-4 mb-lg-0" style="height: 100px;">
                    <div class="d-inline-flex text-left">
                        <i class="fa fa-2x fa-envelope text-white m-0 mr-3"></i>
                        <div class="d-flex flex-column">
                            <h5 class="text-white font-weight-medium">Email Us</h5>
                            <p class="m-0 text-white">dry.me@gmail.com</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-center justify-content-center bg-secondary mb-4 mb-lg-0" style="height: 100px;">
                    <div class="d-inline-flex text-left">
                        <i class="fa fa-2x fa-phone-alt text-white m-0 mr-3"></i>
                        <div class="d-flex flex-column">
                            <h5 class="text-white font-weight-medium">Call Us</h5>
                            <p class="m-0 text-white">+63 123 456 789</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact Info End -->


    <!-- About Start -->
    <div class="container-fluid py-5">
        <div class="container pt-0 pt-lg-4">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <img class="img-fluid" src="img/about.jpg" alt="">
                </div>
                <div class="col-lg-7 mt-5 mt-lg-0 pl-lg-5">
                    <h6 class="text-secondary text-uppercase font-weight-medium mb-3">Learn About Us</h6>
                    <h1 class="mb-4">We Are Quality Laundry Provider In Your City</h1>
                    <h5 class="font-weight-medium font-italic mb-4">At DRYME, we believe laundry should be simple, convenient, and worry-free. We're a tech-powered laundry service that blends modern automation with premium care to bring you the cleanest clothes—on time, every time.</h5>
                    <p class="mb-2">Whether it's your everyday wear, delicate suits, or heavy curtains, we handle each item with the utmost care and professionalism. Our services are designed to meet the needs of busy individuals and families who deserve more time for the things that matter most. Driven by quality, speed, and customer satisfaction, we take pride in delivering fresh, crisp laundry straight to your door. Let us handle the dirty work—so you don't have to.</p>
                    <div class="row">
                        <div class="col-sm-6 pt-3">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-check text-primary mr-2"></i>
                                <p class="text-secondary font-weight-medium m-0">Quality Laundry Service</p>
                            </div>
                        </div>
                        <div class="col-sm-6 pt-3">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-check text-primary mr-2"></i>
                                <p class="text-secondary font-weight-medium m-0">Express Fast Delivery</p>
                            </div>
                        </div>
                        <div class="col-sm-6 pt-3">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-check text-primary mr-2"></i>
                                <p class="text-secondary font-weight-medium m-0">Highly Professional Staff</p>
                            </div>
                        </div>
                        <div class="col-sm-6 pt-3">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-check text-primary mr-2"></i>
                                <p class="text-secondary font-weight-medium m-0">100% Satisfaction Gguarantee</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->


    <!-- Services Start -->
    <div class="container-fluid pt-5 pb-3">
        <div class="container">
            <h6 class="text-secondary text-uppercase text-center font-weight-medium mb-3">Our Services</h6>
            <h1 class="display-4 text-center mb-5">What We Offer</h1>
            <div class="row">
                <!-- Dry Cleaning -->
                <div class="col-lg-3 col-md-6 pb-1">
                    <div class="service-card d-flex flex-column align-items-center justify-content-center text-center bg-light mb-4 px-4 position-relative" style="height: 300px;">
                        <div class="service-icon d-inline-flex align-items-center justify-content-center bg-white shadow rounded-circle mb-4" style="width: 100px; height: 100px;">
                            <i class="fa fa-3x fa-cloud-sun text-secondary"></i>
                        </div>
                        <h4 class="font-weight-bold m-0">Dry Cleaning</h4>
                        <div class="service-description">
                            <p class="m-0">Gentle care for delicate fabrics – no water, just fresh results.</p>
                        </div>
                    </div>
                </div>

                <!-- Wash & Laundry -->
                <div class="col-lg-3 col-md-6 pb-1">
                    <div class="service-card d-flex flex-column align-items-center justify-content-center text-center bg-light mb-4 px-4 position-relative" style="height: 300px;">
                        <div class="service-icon d-inline-flex align-items-center justify-content-center bg-white shadow rounded-circle mb-4" style="width: 100px; height: 100px;">
                            <i class="fas fa-3x fa-soap text-secondary"></i>
                        </div>
                        <h4 class="font-weight-bold m-0">Wash & Laundry</h4>
                        <div class="service-description">
                            <p class="m-0">Thorough wash and fold – your everyday laundry made effortless.</p>
                        </div>
                    </div>
                </div>

                <!-- Curtain Laundry -->
                <div class="col-lg-3 col-md-6 pb-1">
                    <div class="service-card d-flex flex-column align-items-center justify-content-center text-center bg-light mb-4 px-4 position-relative" style="height: 300px;">
                        <div class="service-icon d-inline-flex align-items-center justify-content-center bg-white shadow rounded-circle mb-4" style="width: 100px; height: 100px;">
                            <i class="fa fa-3x fa-wind text-secondary"></i>
                        </div>
                        <h4 class="font-weight-bold m-0">Curtain Laundry</h4>
                        <div class="service-description">
                            <p class="m-0">Deep clean your curtains to keep your space fresh and dust-free.</p>
                        </div>
                    </div>
                </div>

                <!-- Suits Cleaning -->
                <div class="col-lg-3 col-md-6 pb-1">
                    <div class="service-card d-flex flex-column align-items-center justify-content-center text-center bg-light mb-4 px-4 position-relative" style="height: 300px;">
                        <div class="service-icon d-inline-flex align-items-center justify-content-center bg-white shadow rounded-circle mb-4" style="width: 100px; height: 100px;">
                            <i class="fa fa-3x fa-tshirt text-secondary"></i>
                        </div>
                        <h4 class="font-weight-bold m-0">Suits Cleaning</h4>
                        <div class="service-description">
                            <p class="m-0">Professional suit care – crisp, clean, and ready to impress.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Services End -->


    <!-- Features Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 m-0 my-lg-5 pt-0 pt-lg-5 pr-lg-5">
                    <h6 class="text-secondary text-uppercase font-weight-medium mb-3">Our Features</h6>
                    <h1 class="mb-4">Why Choose Us</h1>
                    <p>“At DRYME, we combine modern convenience, professional care, and a passion for cleanliness to give you a laundry experience you can truly rely on.”</p>
                    <div class="row">
                        <div class="col-sm-6 mb-4">
                            <h1 class="text-secondary" data-toggle="counter-up">10</h1>
                            <h5 class="font-weight-bold">Years Expereince</h5>
                        </div>
                        <div class="col-sm-6 mb-4">
                            <h1 class="text-secondary" data-toggle="counter-up">250</h1>
                            <h5 class="font-weight-bold">Expert Worker</h5>
                        </div>
                        <div class="col-sm-6 mb-4">
                            <h1 class="text-secondary" data-toggle="counter-up">1250</h1>
                            <h5 class="font-weight-bold">Happy Clients</h5>
                        </div>
                        <div class="col-sm-6 mb-4">
                            <h1 class="text-secondary" data-toggle="counter-up">9550</h1>
                            <h5 class="font-weight-bold">Dry Cleaning</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="d-flex flex-column align-items-center justify-content-center bg-secondary h-100 py-5 px-3">
                        <i class="fa fa-5x fa-certificate text-white mb-5"></i>
                        <h1 class="display-1 text-white mb-3">10+</h1>
                        <h1 class="text-white m-0">Years Experience</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Features End -->


    <!-- Working Process Start -->
    <div class="container-fluid pt-5">
        <div class="container">
            <h6 class="text-secondary text-uppercase text-center font-weight-medium mb-3">Working Process</h6>
            <h1 class="display-4 text-center mb-5">How We Work</h1>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center mb-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white border border-light shadow rounded-circle mb-4" style="width: 150px; height: 150px; border-width: 15px !important;">
                            <h2 class="display-2 text-secondary m-0">1</h2>
                        </div>
                        <h3 class="font-weight-bold m-0 mt-2">Order Place</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center mb-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white border border-light shadow rounded-circle mb-4" style="width: 150px; height: 150px; border-width: 15px !important;">
                            <h2 class="display-2 text-secondary m-0">2</h2>
                        </div>
                        <h3 class="font-weight-bold m-0 mt-2">Free Pick Up</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center mb-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white border border-light shadow rounded-circle mb-4" style="width: 150px; height: 150px; border-width: 15px !important;">
                            <h2 class="display-2 text-secondary m-0">3</h2>
                        </div>
                        <h3 class="font-weight-bold m-0 mt-2">Dry Cleaning</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center mb-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white border border-light shadow rounded-circle mb-4" style="width: 150px; height: 150px; border-width: 15px !important;">
                            <h2 class="display-2 text-secondary m-0">4</h2>
                        </div>
                        <h3 class="font-weight-bold m-0 mt-2">Free Delivery</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Working Process End -->


    <!-- Pricing Plan Start -->
    <div class="container-fluid pt-5 pb-3">
        <div class="container">
            <h6 class="text-secondary text-uppercase text-center font-weight-medium mb-3">Our Pricing Plan</h6>
            <h1 class="display-4 text-center mb-5">The Best Price</h1>
            <div class="row">
                <div class="col-lg-4 mb-4 d-flex">
                    <div class="bg-white text-center mb-2 pt-4 w-100 d-flex flex-column shadow-lg" style="border-radius: 15px;">
                        <div class="d-inline-flex flex-column align-items-center justify-content-center bg-secondary rounded-circle shadow mt-2 mb-4 mx-auto" style="width: 200px; height: 200px; border: 15px solid #ffffff;">
                            <h3 class="text-white">Normal</h3>
                            <h1 class="display-4 text-white mb-0 text-center">
                                <small class="align-top" style="font-size: 22px; line-height: 45px;">₱</small>210<small class="align-bottom" style="font-size: 16px; line-height: 40px;">.00</small>
                            </h1>
                        </div>
                        <div class="d-flex flex-column align-items-center py-3 flex-grow-1">
                            <p>Automated Washing</p>
                            <p>Dry Cleaning</p>
                            <p>Iron & Fold</p>
                        </div>
                        <!-- Basic package button -->
                        <div class="mt-auto p-4">
                            <a href="place_order.php?priority=normal" class="btn btn-secondary py-2 px-5" style="color: #000000 !important;">
                                <span class="order-text">Order Now</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4 d-flex">
                    <div class="bg-white text-center mb-2 pt-4 w-100 d-flex flex-column shadow-lg" style="border-radius: 15px;">
                        <div class="d-inline-flex flex-column align-items-center justify-content-center bg-primary rounded-circle shadow mt-2 mb-4 mx-auto" style="width: 200px; height: 200px; border: 15px solid #ffffff;">
                            <h3 class="text-white">Express</h3>
                            <h1 class="display-4 text-white mb-0 text-center">
                                <small class="align-top" style="font-size: 22px; line-height: 45px;">₱</small>210<small class="align-top" style="font-size: 16px; line-height: 40px;">+25%</small>
                            </h1>
                        </div>
                        <div class="d-flex flex-column align-items-center py-3 flex-grow-1">
                            <p>Premium Washing</p>
                            <p>Dry Cleaning</p>
                            <p>Iron & Fold</p>
                            <p>Express Service</p>
                            <p>Free Delivery</p>
                        </div>
                        <!-- Standard package button -->
                        <div class="mt-auto p-4">
                            <a href="place_order.php?priority=express" class="btn btn-primary py-2 px-5" style="color: #000000 !important;">
                                <span class="order-text">Order Now</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4 d-flex">
                    <div class="bg-white text-center mb-2 pt-4 w-100 d-flex flex-column shadow-lg" style="border-radius: 15px;">
                        <div class="d-inline-flex flex-column align-items-center justify-content-center rounded-circle shadow mt-2 mb-4 mx-auto" style="width: 200px; height: 200px; border: 15px solid #ffffff; background-color: #DAA520;">
                            <h3 class="text-white">Rush</h3>
                            <h1 class="display-4 text-white mb-0 text-center">
                                <small class="align-top" style="font-size: 22px; line-height: 45px;">₱</small>210<small class="align-top" style="font-size: 16px; line-height: 40px;">+50%</small>
                            </h1>
                        </div>
                        <div class="d-flex flex-column align-items-center py-3 flex-grow-1">
                            <p>Luxury Washing</p>
                            <p>Premium Dry Cleaning</p>
                            <p>Professional Iron & Fold</p>
                            <p>Priority Service</p>
                            <p>Free Pickup & Delivery</p>
                        </div>
                        <!-- Premium package button -->
                        <div class="mt-auto p-4">
                            <a href="place_order.php?priority=rush" class="btn py-2 px-5" style="background-color: #DAA520; color: #000000 !important;">
                                <span class="order-text">Order Now</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pricing Plan End -->


    <!-- Footer Start -->
    <div class="container-fluid bg-primary text-white mt-5 pt-5 px-sm-3 px-md-5">
        <div class="row pt-5">
            <div class="col-lg-3 col-md-6 mb-5">
                <a href="">
                    <h1 class="text-secondary mb-3"><span class="text-white">DRY</span>ME</h1>
                </a>
                <p>"We don’t just clean clothes—we care for them."</p>
                <div class="d-flex justify-content-start mt-4">
                    <a class="btn btn-outline-light rounded-circle text-center mr-2 px-0" style="width: 38px; height: 38px;" href="#"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-outline-light rounded-circle text-center mr-2 px-0" style="width: 38px; height: 38px;" href="#"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-outline-light rounded-circle text-center mr-2 px-0" style="width: 38px; height: 38px;" href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-outline-light rounded-circle text-center mr-2 px-0" style="width: 38px; height: 38px;" href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-white mb-4">Get In Touch</h4>
                <p><i class="fa fa-map-marker-alt mr-2"></i>Cebu City, Philippines</p>
                <p><i class="fa fa-phone-alt mr-2"></i>+63 123 456 789</p>
                <p><i class="fa fa-envelope mr-2"></i>dry.me@gmail.com</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-white mb-4">Quick Links</h4>
                <div class="d-flex flex-column justify-content-start">
                    <a class="text-white mb-2" href="index.php"><i class="fa fa-angle-right mr-2"></i>Home</a>
                    <a class="text-white mb-2" href="about.php"><i class="fa fa-angle-right mr-2"></i>About Us</a>
                    <a class="text-white mb-2" href="service.php"><i class="fa fa-angle-right mr-2"></i>Services</a>
                    <a class="text-white mb-2" href="pricing.php"><i class="fa fa-angle-right mr-2"></i>Pricing</a>
                    <a class="text-white" href="contact.php"><i class="fa fa-angle-right mr-2"></i>Contact Us</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-white mb-4">Newsletter</h4>
                <form action="">
                    <div class="form-group">
                        <input type="text" class="form-control border-0" placeholder="Your Name" required="required" />
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control border-0" placeholder="Your Email" required="required" />
                    </div>
                    <div>
                        <button class="btn btn-lg btn-secondary btn-block border-0" type="submit">Submit Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="container-fluid bg-dark text-white py-4 px-sm-3 px-md-5">
        <p class="m-0 text-center text-white">
            &copy; <a class="text-white font-weight-medium" href="#">dryme.com</a>. All Rights Reserved.

            <!-- Footer End -->


            <!-- Back to Top -->
            <a href="#" class="btn btn-lg btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>


            <!-- JavaScript Libraries -->
            <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
            <script src="lib/easing/easing.min.js"></script>
            <script src="lib/waypoints/waypoints.min.js"></script>
            <script src="lib/counterup/counterup.min.js"></script>
            <script src="lib/owlcarousel/owl.carousel.min.js"></script>

            <!-- Contact Javascript File -->
            <script src="mail/jqBootstrapValidation.min.js"></script>
            <script src="mail/contact.js"></script>

            <!-- Main Javascript -->
            <script src="js/main.js"></script>
</body>

</html>