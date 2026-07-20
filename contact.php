<?php include 'includes/header.php'; ?>

<section class="contact-section">
    <div class="container">
        <div class="about-card-header" style="margin-bottom: 0;">
            <h2 class="section-title" style="text-align:center;">Contact Us</h2>
            <p class="contact-subtitle" style="text-align:center;">We'd love to hear from you! Reach out for any questions, feedback, or support.</p>
        </div>
        <div class="contact-main-flex">
            <div class="contact-left">
                <div class="contact-info-card luxury-card">
                    <h3>Get in Touch</h3>
                    <div class="contact-details">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Fashion Street, Style City, SC 12345</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span>+1 (234) 567-8900</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>info@lookfab.com</span>
                        </div>
                    </div>
                    <div class="business-hours">
                        <h4>Business Hours</h4>
                        <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                        <p>Saturday: 10:00 AM - 4:00 PM</p>
                        <p>Sunday: Closed</p>
                    </div>
                    <div class="social-icons" style="margin-top:18px;">
                        <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" title="Pinterest"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                <div class="contact-form-card luxury-card">
                    <h3>Send Us a Message</h3>
                    <form action="php/contact.php" method="POST">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn">Send Message</button>
                    </form>
                </div>
            </div>
            <div class="contact-map-card luxury-card">
                <h3>Our Location</h3>
                <div class="map-embed">
                    <iframe
                        src="https://www.google.com/maps?q=123+Fashion+Street,+Style+City,+SC+12345&output=embed"
                        width="100%" height="320" style="border:0; border-radius: 14px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>