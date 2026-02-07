<?php
include('../../backend/includes/db.php');

$error = '';
$success = '';

if(isset($_POST['send_msg'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    
    if(empty($name) || empty($email) || empty($subject) || empty($msg)) {
        $error = 'All fields are required';
    } else {
        $result = mysqli_query($conn, "INSERT INTO messages (user_name, user_email, message) VALUES ('$name', '$email', '[Subject: $subject] $msg')");
        if($result) {
            $success = 'Message sent successfully! We will get back to you soon.';
        } else {
            $error = 'Failed to send message. Please try again.';
        }
    }
}

$page_title = 'Contact Us';
include('../../backend/includes/header.php');
?>

<div class="container my-5">
    <div class="section-header">
        <h2><i class="fas fa-envelope"></i> Contact Us</h2>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h3 class="mb-4">Get in Touch</h3>

                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" id="contactForm">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Your name" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Your email" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="Message subject" required>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="6" placeholder="Your message here..." required></textarea>
                        </div>

                        <button type="submit" name="send_msg" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3"><i class="fas fa-phone text-primary"></i> Phone</h5>
                    <p class="card-text">+1 (555) 123-4567</p>
                </div>
            </div>

            <div class="card shadow-lg border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3"><i class="fas fa-envelope text-primary"></i> Email</h5>
                    <p class="card-text">
                        <a href="mailto:support@electrostore.com" class="text-decoration-none">support@electrostore.com</a>
                    </p>
                </div>
            </div>

            <div class="card shadow-lg border-0">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3"><i class="fas fa-map-marker-alt text-primary"></i> Address</h5>
                    <p class="card-text">
                        123 Tech Street<br>
                        Silicon Valley, CA 94025<br>
                        United States
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../backend/includes/footer.php'); ?>