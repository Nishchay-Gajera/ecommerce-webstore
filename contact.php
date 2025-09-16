<?php
require_once 'includes/header.php';

$message_sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    // In a real application, you would add code here to send an email.
    // For this example, we'll just simulate a success message.
    $message_sent = true;
}
?>

<main class="static-page-content">
    <div class="container">
        <div class="static-page-header">
            <h1>Contact Us</h1>
            <p>We'd love to hear from you! Whether you have a question about our products, your order, or anything else, our team is ready to answer all your questions.</p>
        </div>

        <div class="contact-layout">
            <div class="contact-form-container">
                <?php if ($message_sent): ?>
                    <div class="success-message-box">
                        Thank you for your message! We'll get back to you shortly.
                    </div>
                <?php else: ?>
                    <form action="contact.php" method="POST">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" class="form-control" rows="6" required></textarea>
                        </div>
                        <button type="submit" name="contact_submit" class="btn-primary">Send Message</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="contact-info-container">
                <div class="contact-info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Our Address</h3>
                    <p>708, Akruti Complex Nr, Sahyadri Flats, Nr, Standium Navrangpura Ahmedabad 380009</p>
                </div>
                <div class="contact-info-item">
                    <i class="fas fa-envelope"></i>
                    <h3>Email Us</h3>
                    <p><a href="mailto:support@divinesyncserve.com">support@divinesyncserve.com</a></p>
                </div>
                <div class="contact-info-item">
                    <i class="fas fa-phone-alt"></i>
                    <h3>Call Us</h3>
                    <p><a href="tel:+911234567890">+91 87358 26083  </a></p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

</body>
</html>
