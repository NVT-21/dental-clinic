<!DOCTYPE html>
<html lang="en">



<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Home - Dental Clinic</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="{{ asset('img/favicon.png') }}" rel="icon">
  <link href="{{ asset('img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
  <link href="{{ asset('vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('css/main.css') }}" rel="stylesheet">
</head>


<body class="index-page">
    @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
    @endif

  <header id="header" class="header sticky-top">

    <div class="topbar d-flex align-items-center">
      <div class="container d-flex justify-content-center justify-content-md-between">
        <div class="contact-info d-flex align-items-center">
          <i class="bi bi-envelope d-flex align-items-center"></i>
          <i class="bi bi-phone d-flex align-items-center ms-4"></i>
        </div>
        <div class="social-links d-none d-md-flex align-items-center">

          <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
    </div><!-- End Top Bar -->

    <div class="branding d-flex align-items-center">

      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="index.html" class="logo d-flex align-items-center me-auto">
          <!-- Uncomment the line below if you also wish to use an image logo -->
          <!-- <img src="public/img/logo.png" alt=""> -->
          <h1 class="sitename">Medilab</h1>
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="#hero" class="active">Home<br></a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#doctors">Doctors</a></li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <a class="cta-btn d-none d-sm-block" href="#appointment">Make an Appointment</a>

      </div>

    </div>

  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section light-background">

      <img src="public/img/hero-bg.jpg" alt="" data-aos="fade-in">

      <div class="container position-relative">

        <div class="welcome position-relative" data-aos="fade-down" data-aos-delay="100">
          <h2>WELCOME TO DENTAL CLINIC</h2>
        </div><!-- End Welcome -->

        <div class="content row gy-4">
          <div class="col-lg-4 d-flex align-items-stretch">
            <div class="why-box" data-aos="zoom-out" data-aos-delay="200">
              <h3>Why Choose Medilab?</h3>
              <p>
                At Dental Clinic, we are committed to providing the highest quality dental care in a comfortable and friendly environment. Our team of experienced
                 dentists uses the latest technology to ensure your oral health is in the best hands.
              </p>
              <div class="text-center">
                <a href="#about" class="more-btn"><span>Learn More</span> <i class="bi bi-chevron-right"></i></a>
              </div>
            </div>
          </div><!-- End Why Box -->

          <div class="col-lg-8 d-flex align-items-stretch">
            <div class="d-flex flex-column justify-content-center">
              <div class="row gy-4">

                <div class="col-xl-4 d-flex align-items-stretch">
                  <div class="icon-box" data-aos="zoom-out" data-aos-delay="300">
                    <i class="bi bi-clipboard-data"></i>
                    <h4>General Dentistry</h4>
                    <p>Comprehensive dental care for the whole family, including check-ups, cleanings, and fillings.</p>
                  </div>
                </div><!-- End Icon Box -->

                <div class="col-xl-4 d-flex align-items-stretch">
                  <div class="icon-box" data-aos="zoom-out" data-aos-delay="400">
                    <i class="bi bi-gem"></i>
                    <h4>Cosmetic Dentistry</h4>
                    <p>Enhance your smile with our cosmetic services, including teeth whitening and veneers.</p>
                  </div>
                </div><!-- End Icon Box -->

                <div class="col-xl-4 d-flex align-items-stretch">
                  <div class="icon-box" data-aos="zoom-out" data-aos-delay="500">
                    <i class="bi bi-inboxes"></i>
                    <h4>Orthodontics</h4>
                    <p>Straighten your teeth with our orthodontic treatments, including braces and Invisalign.</p>
                  </div>
                </div><!-- End Icon Box -->

              </div>
            </div>
          </div>
        </div><!-- End  Content-->

      </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">

      <div class="container">

        <div class="row gy-4 gx-5">

          <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up" data-aos-delay="200">
          <img src="{{ asset('img/about.jpg') }}" class="img-fluid" alt="">

           
          </div>

          <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
            <h3>About Us</h3>
            <p>
              Dental Clinic has been serving the community for over 20 years. Our mission is to provide exceptional dental care in a warm and welcoming environment. We believe in building
               long-term relationships with our patients and ensuring their comfort and satisfaction.
            <ul>
              <li>
                <i class="fa-solid fa-vial-circle-check"></i>
                <div>
                  <h5>Experienced Dentists</h5>
                  <p>Our team of dentists has extensive experience in all areas of dentistry.</p>
                </div>
              </li>
              <li>
                <i class="fa-solid fa-pump-medical"></i>
                <div>
                  <h5>Patient-Centered Care</h5>
                  <p>We prioritize your comfort and satisfaction in every treatment.</p>
                </div>
              </li>
              <li>
                <i class="fa-solid fa-heart-circle-xmark"></i>
                <div>
                  <h5>Advanced Technology</h5>
                  <p>We use the latest dental technology to ensure the best results.</p>
                </div>
              </li>
            </ul>
          </div>

        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Stats Section -->
  

    <!-- Services Section -->
    <section id="services" class="services section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Services</h2>
        <p>We offer a wide range of dental services to meet your needs.</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-item  position-relative">
              <div class="icon">
                <i class="fas fa-heartbeat"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>General Dentistry</h3>
              </a>
              <p>Routine check-ups, cleanings, fillings, and more to maintain your oral health.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-pills"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Cosmetic Dentistry</h3>
              </a>
              <p>Enhance your smile with teeth whitening, veneers, and other cosmetic treatments.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-hospital-user"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Orthodontics</h3>
              </a>
              <p>Straighten your teeth with braces or Invisalign for a perfect smile.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-dna"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Implants</h3>
              </a>
              <p>Replace missing teeth with durable and natural-looking dental implants.</p>
              <a href="#" class="stretched-link"></a>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-wheelchair"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Oral Surgery</h3>
              </a>
              <p>Expert surgical procedures, including wisdom teeth removal and jaw surgery.</p>
              <a href="#" class="stretched-link"></a>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-notes-medical"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Pediatric Dentistry</h3>
              </a>
              <p>Specialized dental care for children to ensure healthy smiles from an early age.</p>
              <a href="#" class="stretched-link"></a>
            </div>
          </div><!-- End Service Item -->

        </div>

      </div>

    </section><!-- /Services Section -->

    <!-- Appointment Section -->
    <section id="appointment" class="appointment section">
  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Book an Appointment</h2>
    <p>Schedule your appointment with our expert doctors at your convenience.</p>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <form action="{{ route('patient.store') }}" method="post" role="form" class="" id="appointmentForm">
      @csrf
      <!-- Search Section -->
      <div class="row">
        <div class="col-md-4 form-group">
          <input type="tel" class="form-control" id="searchPhone" placeholder="Search by Phone Number" required>
        </div>
        <div class="col-md-2 form-group mt-3 mt-md-0">
          <button type="button" class="btn btn-primary" id="searchButton">Search</button>
        </div>
      </div>
      <!-- Patient Table -->
      <div id="patientTable" class="mt-4" style="display: none;">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Full Name</th>
              <th>Phone Number</th>
              <th>Birthdate</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="patientTableBody"></tbody>
        </table>
      </div>

      <div class="row">
        <div class="col-md-4 form-group">
          <input type="text" name="fullname" class="form-control" id="fullname" placeholder="Your Name" required>
        </div>
        <div class="col-md-4 form-group mt-3 mt-md-0">
          <input type="email" class="form-control" name="email" id="email" placeholder="Your Email">
        </div>
        <div class="col-md-4 form-group mt-3 mt-md-0">
          <input type="tel" class="form-control" name="phoneNumber" id="phoneNumber" placeholder="Your Phone" required>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 form-group mt-3">
          <label for="birthdate">Birthday</label>
          <input type="date" name="birthdate" class="form-control" id="birthdate" placeholder="Enter your birthday" required>
        </div>
        <div class="col-md-4 form-group mt-3">
          <label for="bookingDate">Preferred Date</label>
          <input type="date" name="bookingDate" class="form-control" id="bookingDate" required>
        </div>
         <div class="col-md-4 form-group mt-3">
  <label for="appointmentTime">Preferred Time (Between 7:00 and 21:00)</label>
  <input type="time" name="appointmentTime" class="form-control" id="appointmentTime"
         required min="07:00" max="21:00">
   <small id="timeError" style="color:red;display:none;">Please select a time between 7:00 and 21:00</small>
</div>
      </div>
        <div class="mt-3">
      <div class="text-center"><button type="submit" class="appointment-btn">Book Appointment</button></div>
      </div>
    </form>
  </div>
</section>

    
    </section><!-- /Appointment Section -->
    <!-- Departments Section -->
  

    <!-- Doctors Section -->
    <section id="doctors" class="doctors section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Meet Our Doctors</h2>
        <p>Our team of highly skilled medical professionals is here to provide the best care.</p>
      </div><!-- End Section Title -->
    
      <div class="container">
    
        <div class="row gy-4">
    
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
    <div class="team-member d-flex align-items-start">
        <div class="pic"><img src="{{ asset('img/doctors/doctors-1.jpg') }}" class="img-fluid" alt=""></div>
        <div class="member-info">
            <h4>Dr. Walter White</h4>
            <span>Chief Medical Officer</span>
            <p>Specialist in general medicine with years of experience in patient care.</p>
            <div class="social">
                <a href=""><i class="bi bi-twitter-x"></i></a>
                <a href=""><i class="bi bi-facebook"></i></a>
                <a href=""><i class="bi bi-instagram"></i></a>
                <a href=""><i class="bi bi-linkedin"></i></a>
            </div>
        </div>
    </div>
</div><!-- End Team Member -->

<div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
    <div class="team-member d-flex align-items-start">
        <div class="pic"><img src="{{ asset('img/doctors/doctors-2.jpg') }}" class="img-fluid" alt=""></div>
        <div class="member-info">
            <h4>Dr. Sarah Johnson</h4>
            <span>Anesthesiologist</span>
            <p>Expert in pain management and anesthesia care for surgical patients.</p>
            <div class="social">
                <a href=""><i class="bi bi-twitter-x"></i></a>
                <a href=""><i class="bi bi-facebook"></i></a>
                <a href=""><i class="bi bi-instagram"></i></a>
                <a href=""><i class="bi bi-linkedin"></i></a>
            </div>
        </div>
    </div>
</div><!-- End Team Member -->

<div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
    <div class="team-member d-flex align-items-start">
        <div class="pic"><img src="{{ asset('img/doctors/doctors-3.jpg') }}" class="img-fluid" alt=""></div>
        <div class="member-info">
            <h4>Dr. William Anderson</h4>
            <span>Cardiologist</span>
            <p>Providing advanced heart care and treatment for cardiovascular diseases.</p>
            <div class="social">
                <a href=""><i class="bi bi-twitter-x"></i></a>
                <a href=""><i class="bi bi-facebook"></i></a>
                <a href=""><i class="bi bi-instagram"></i></a>
                <a href=""><i class="bi bi-linkedin"></i></a>
            </div>
        </div>
    </div>
</div><!-- End Team Member -->

<div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
    <div class="team-member d-flex align-items-start">
        <div class="pic"><img src="{{ asset('img/doctors/doctors-4.jpg') }}" class="img-fluid" alt=""></div>
        <div class="member-info">
            <h4>Dr. Amanda Jepson</h4>
            <span>Neurosurgeon</span>
            <p>Specializing in brain and spinal cord surgeries for neurological conditions.</p>
            <div class="social">
                <a href=""><i class="bi bi-twitter-x"></i></a>
                <a href=""><i class="bi bi-facebook"></i></a>
                <a href=""><i class="bi bi-instagram"></i></a>
                <a href=""><i class="bi bi-linkedin"></i></a>
            </div>
        </div>
    </div>
</div><!-- End Team Member -->

    
        </div>
    
      </div>
    
    </section><!-- /Doctors Section -->

    <!-- Faq Section -->
   

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section">

      <div class="container">
    
        <div class="row align-items-center">
    
          <div class="col-lg-5 info" data-aos="fade-up" data-aos-delay="100">
            <h3>Patient Testimonials</h3>
            <p>Hear from our satisfied patients about their experiences at our clinic.</p>
          </div>
    
          <div class="col-lg-7" data-aos="fade-up" data-aos-delay="200">
            <div class="swiper init-swiper">
              <div class="swiper-wrapper">
    
                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <div class="d-flex">
                    <img src="{{ asset('img/testimonials/testimonials-1.jpg') }}" class="testimonial-img flex-shrink-0" alt="">

                      <div>
                        <h3>Saul Goodman</h3>
                        <h4>CEO & Founder</h4>
                        <p>"Excellent service and professional staff. Highly recommended!"</p>
                      </div>
                    </div>
                  </div>
                </div><!-- End testimonial item -->
    
              </div>
            </div>
          </div>
    
        </div>
    
      </div>
    
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="gallery section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Gallery</h2>
      </div><!-- End Section Title -->

      <div class="container-fluid" data-aos="fade-up" data-aos-delay="100">

<div class="row g-0">

  <div class="col-lg-3 col-md-4">
    <div class="gallery-item">
      <a href="{{ asset('img/gallery/gallery-1.jpg') }}" class="glightbox" data-gallery="images-gallery">
        <img src="{{ asset('img/gallery/gallery-1.jpg') }}" alt="" class="img-fluid">
      </a>
    </div>
  </div><!-- End Gallery Item -->

  <div class="col-lg-3 col-md-4">
    <div class="gallery-item">
      <a href="{{ asset('img/gallery/gallery-2.jpg') }}" class="glightbox" data-gallery="images-gallery">
        <img src="{{ asset('img/gallery/gallery-2.jpg') }}" alt="" class="img-fluid">
      </a>
    </div>
  </div><!-- End Gallery Item -->

  <div class="col-lg-3 col-md-4">
    <div class="gallery-item">
      <a href="{{ asset('img/gallery/gallery-3.jpg') }}" class="glightbox" data-gallery="images-gallery">
        <img src="{{ asset('img/gallery/gallery-3.jpg') }}" alt="" class="img-fluid">
      </a>
    </div>
  </div><!-- End Gallery Item -->

  <div class="col-lg-3 col-md-4">
    <div class="gallery-item">
      <a href="{{ asset('img/gallery/gallery-4.jpg') }}" class="glightbox" data-gallery="images-gallery">
        <img src="{{ asset('img/gallery/gallery-4.jpg') }}" alt="" class="img-fluid">
      </a>
    </div>
  </div><!-- End Gallery Item -->

  <div class="col-lg-3 col-md-4">
    <div class="gallery-item">
      <a href="{{ asset('img/gallery/gallery-5.jpg') }}" class="glightbox" data-gallery="images-gallery">
        <img src="{{ asset('img/gallery/gallery-5.jpg') }}" alt="" class="img-fluid">
      </a>
    </div>
  </div><!-- End Gallery Item -->

  <div class="col-lg-3 col-md-4">
    <div class="gallery-item">
      <a href="{{ asset('img/gallery/gallery-6.jpg') }}" class="glightbox" data-gallery="images-gallery">
        <img src="{{ asset('img/gallery/gallery-6.jpg') }}" alt="" class="img-fluid">
      </a>
    </div>
  </div><!-- End Gallery Item -->

  <div class="col-lg-3 col-md-4">
    <div class="gallery-item">
      <a href="{{ asset('img/gallery/gallery-7.jpg') }}" class="glightbox" data-gallery="images-gallery">
        <img src="{{ asset('img/gallery/gallery-7.jpg') }}" alt="" class="img-fluid">
      </a>
    </div>
  </div><!-- End Gallery Item -->

  <div class="col-lg-3 col-md-4">
    <div class="gallery-item">
      <a href="{{ asset('img/gallery/gallery-8.jpg') }}" class="glightbox" data-gallery="images-gallery">
        <img src="{{ asset('img/gallery/gallery-8.jpg') }}" alt="" class="img-fluid">
      </a>
    </div>
  </div><!-- End Gallery Item -->

</div>

</div>

    </section><!-- /Gallery Section -->

    <!-- Contact Section -->
   
  </main>

  <footer id="footer" class="footer light-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="logo d-flex align-items-center">
            <span class="sitename">Medilab</span>
          </a>
          <div class="footer-contact pt-3">
            <p>A108 Adam Street</p>
            <p>New York, NY 535022</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+1 5589 55488 55</span></p>
            <p><strong>Email:</strong> <span>info@example.com</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About us</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Terms of service</a></li>
            <li><a href="#">Privacy policy</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><a href="#">Web Design</a></li>
            <li><a href="#">Web Development</a></li>
            <li><a href="#">Product Management</a></li>
            <li><a href="#">Marketing</a></li>
            <li><a href="#">Graphic Design</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Hic solutasetp</h4>
          <ul>
            <li><a href="#">Molestiae accusamus iure</a></li>
            <li><a href="#">Excepturi dignissimos</a></li>
            <li><a href="#">Suscipit distinctio</a></li>
            <li><a href="#">Dilecta</a></li>
            <li><a href="#">Sit quas consectetur</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Nobis illum</h4>
          <ul>
            <li><a href="#">Ipsam</a></li>
            <li><a href="#">Laudantium dolorum</a></li>
            <li><a href="#">Dinera</a></li>
            <li><a href="#">Trodelas</a></li>
            <li><a href="#">Flexo</a></li>
          </ul>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Medilab</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> Distributed by <a href=“https://themewagon.com>ThemeWagon
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/php-email-form/validate.js') }}"></script>
<script src="{{ asset('vendor/aos/aos.js') }}"></script>
<script src="{{ asset('vendor/glightbox/js/glightbox.min.js') }}"></script>
<script src="{{ asset('vendor/purecounter/purecounter_vanilla.js') }}"></script>
<script src="{{ asset('vendor/swiper/swiper-bundle.min.js') }}"></script>
<script>
  window.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.getElementById('bookingDate');
    const today = new Date().toISOString().split('T')[0];
    dateInput.min = today;
  });
   
</script>
<script>
document.getElementById('appointmentTime').addEventListener('input', function() {
    const input = this.value;
    const error = document.getElementById('timeError');
    if (input < "07:00" || input > "21:00") {
        error.style.display = 'block';
        this.setCustomValidity('Please choose a time between 07:00 and 21:00.');
    } else {
        error.style.display = 'none';
        this.setCustomValidity('');
    }
});
</script>
<!-- Main JS File -->
<script src="{{ asset('js/main.js') }}"></script>


</body>

</html>