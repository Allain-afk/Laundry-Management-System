<?php
session_start();
require_once 'includes/db_connect.php';
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
                        <a href="index.php" class="nav-item nav-link">Home</a>
                        <a href="about.php" class="nav-item nav-link">About</a>
                        <a href="service.php" class="nav-item nav-link active">Services</a>
                        <a href="pricing.php" class="nav-item nav-link">Pricing</a>
                        <a href="contact.php" class="nav-item nav-link">Contact</a>
                        
                        <!-- Add User Profile Dropdown -->
                        <?php
                        if(isset($_SESSION['user_id'])) { ?>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-user"></i> <?php echo $_SESSION['username']; ?>
                                </a>
                                <div class="dropdown-menu border-0 rounded-0 m-0">
                                    <div class="px-3 py-2">
                                        <p class="mb-1"><strong>Username:</strong> <?php echo $_SESSION['username']; ?></p>
                                        <p class="mb-1"><strong>Email:</strong> <?php echo $_SESSION['email']; ?></p>
                                        <?php if(isset($_SESSION['full_name'])): ?>
                                            <p class="mb-1"><strong>Full Name:</strong> <?php echo $_SESSION['full_name']; ?></p>
                                        <?php endif; ?>
                                        <?php if(isset($_SESSION['phone'])): ?>
                                            <p class="mb-1"><strong>Phone:</strong> <?php echo $_SESSION['phone']; ?></p>
                                        <?php endif; ?>
                                        <?php if(isset($_SESSION['address'])): ?>
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


    <!-- Page Header Start -->
    <div class="page-header container-fluid bg-secondary pt-2 pt-lg-5 pb-2 mb-5">
        <div class="container py-5">
            <div class="row align-items-center py-4">
                <div class="col-md-6 text-center text-md-left">
                    <h1 class="mb-4 mb-md-0 text-white">Our Services</h1>
                </div>
                <div class="col-md-6 text-center text-md-right">
                    <div class="d-inline-flex align-items-center">
                        <a class="btn text-white" href="index.php">Home</a>
                        <i class="fas fa-angle-right text-white"></i>
                        <a class="btn text-white disabled" href="">Services</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header Start -->


    <!-- Services Start -->
    <div class="container-fluid pt-5 pb-3">
        <div class="container">
            <h6 class="text-secondary text-uppercase text-center font-weight-medium mb-3">Our Services</h6>
            <h1 class="display-4 text-center mb-5">What We Offer</h1>
            <!-- Replace the existing services with these -->
            <div class="row">
                <!-- Dry Cleaning -->
                <!-- Example for one card, repeat for others -->
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
                    <div class="service-card d-flex flex-column align-items-center justify-content-center text-center bg-light mb-4 px-4" style="height: 300px;">
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
                    <div class="service-card d-flex flex-column align-items-center justify-content-center text-center bg-light mb-4 px-4" style="height: 300px;">
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
                    <div class="service-card d-flex flex-column align-items-center justify-content-center text-center bg-light mb-4 px-4" style="height: 300px;">
                        <div class="service-icon d-inline-flex align-items-center justify-content-center bg-white shadow rounded-circle mb-4" style="width: 100px; height: 100px;">
                            <i class="fa fa-3x fa-tshirt text-secondary"></i>
                        </div>
                        <h4 class="font-weight-bold m-0">Suits Cleaning</h4>
                        <div class="service-description">
                            <p class="m-0">Professional suit care – crisp, clean, and ready to impress.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Shoe Cleaning -->
                <div class="col-lg-3 col-md-6 pb-1">
                    <div class="service-card d-flex flex-column align-items-center justify-content-center text-center bg-light mb-4 px-4" style="height: 300px;">
                        <div class="service-icon d-inline-flex align-items-center justify-content-center bg-white shadow rounded-circle mb-4" style="width: 100px; height: 100px;">
                            <i class="fas fa-3x fa-shoe-prints text-secondary"></i>
                        </div>
                        <h4 class="font-weight-bold m-0">Shoe Cleaning</h4>
                        <div class="service-description">
                            <p class="m-0">Restore your kicks – clean, deodorized, and ready to wear.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Stuffed Toy Cleaning -->
                <div class="col-lg-3 col-md-6 pb-1">
                    <div class="service-card d-flex flex-column align-items-center justify-content-center text-center bg-light mb-4 px-4" style="height: 300px;">
                        <div class="service-icon d-inline-flex align-items-center justify-content-center bg-white shadow rounded-circle mb-4" style="width: 100px; height: 100px;">
                            <i class="fas fa-3x fa-baby text-secondary"></i>
                        </div>
                        <h4 class="font-weight-bold m-0">Stuffed Toy Cleaning</h4>
                        <div class="service-description">
                            <p class="m-0">Safe and soft – plush toy cleaning for kids and collectors alike.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Bedding & Comforter -->
                <div class="col-lg-3 col-md-6 pb-1">
                    <div class="service-card d-flex flex-column align-items-center justify-content-center text-center bg-light mb-4 px-4" style="height: 300px;">
                        <div class="service-icon d-inline-flex align-items-center justify-content-center bg-white shadow rounded-circle mb-4" style="width: 100px; height: 100px;">
                            <i class="fas fa-3x fa-bed text-secondary"></i>
                        </div>
                        <h4 class="font-weight-bold m-0">Bedding & Comforter</h4>
                        <div class="service-description">
                            <p class="m-0">Fluffy and fresh – for a cleaner, cozier sleep.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Jacket & Outerwear -->
                <div class="col-lg-3 col-md-6 pb-1">
                    <div class="service-card d-flex flex-column align-items-center justify-content-center text-center bg-light mb-4 px-4" style="height: 300px;">
                        <div class="service-icon d-inline-flex align-items-center justify-content-center bg-white shadow rounded-circle mb-4" style="width: 100px; height: 100px;">
                            <i class="fas fa-3x fa-mitten text-secondary"></i>
                        </div>
                        <h4 class="font-weight-bold m-0">Jacket & Outerwear</h4>
                        <div class="service-description">
                            <p class="m-0">Winter wear cleaned and cared for – no shrinkage, just warmth.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Services End -->


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
            &copy; <a class="text-white font-weight-medium" href="#">dryme.com</a>. All Rights Reserved.</p>
    </div>
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

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>