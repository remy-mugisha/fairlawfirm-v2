    <!-- ============ FOOTER ============ -->
    <footer class="fl-footer">
        <div class="fl-container">
            <div class="fl-footer__grid">

                <!-- Column 1: Brand -->
                <div class="fl-footer__brand-col">
                    <a href="index.php" class="fl-footer__logo" aria-label="Fair Law Firm LTD Home">
                        <img src="assets/images/logo-white-1.png" alt="Fair Law Firm LTD" width="180">
                    </a>
                    <p class="fl-footer__brand-desc">
                        <?= __('Fair Law Firm LTD is a Rwandan law and property management firm founded in 2021. We provide trusted legal counsel and professional property management solutions across Rwanda.') ?>
                    </p>
                    <div class="fl-footer__social">
                        <a href="https://x.com/fairlawfirmltd" target="_blank" rel="noopener" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="https://www.linkedin.com/in/fair-law-firm-ltd-6154b3317/" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="https://www.instagram.com/fair_law_firm_ltd/" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>

                <!-- Column 2: Legal Services -->
                <div>
                    <h4 class="fl-footer__heading"><?= __('Legal Services') ?></h4>
                    <ul class="fl-footer__links">
                        <li><a href="legal_services.php"><?= __('Legal Advisory') ?></a></li>
                        <li><a href="legal_services.php"><?= __('Court Representation') ?></a></li>
                        <li><a href="legal_services.php"><?= __('Mediation & Conciliation') ?></a></li>
                        <li><a href="legal_services.php"><?= __('Business Transactions') ?></a></li>
                        <li><a href="legal_services.php"><?= __('Contract Drafting') ?></a></li>
                        <li><a href="legal_services.php"><?= __('Legal Consultation') ?></a></li>
                    </ul>
                </div>

                <!-- Column 3: Property Management -->
                <div>
                    <h4 class="fl-footer__heading"><?= __('Property Management') ?></h4>
                    <ul class="fl-footer__links">
                        <li><a href="property_service.php"><?= __('Rental Management') ?></a></li>
                        <li><a href="property_service.php"><?= __('Sales Management') ?></a></li>
                        <li><a href="property_service.php"><?= __('Rent Recovery') ?></a></li>
                        <li><a href="property_service.php"><?= __('Marketing & Advisory') ?></a></li>
                        <li><a href="property.php"><?= __('Browse Properties') ?></a></li>
                        <li><a href="manage_property.php"><?= __('Manage Properties') ?></a></li>
                    </ul>
                </div>

                <!-- Column 4: Contact Info -->
                <div>
                    <h4 class="fl-footer__heading"><?= __('Contact Info') ?></h4>
                    <div class="fl-footer__contact-item">
                        <i class="fa fa-location-dot"></i>
                        <div>KG 194 St, Kigali, Kimironko<br>Near BPR Branch, Rwanda</div>
                    </div>
                    <div class="fl-footer__contact-item">
                        <i class="fa fa-phone"></i>
                        <div>
                            <a href="tel:+250788411095">+250 788 411 095</a><br>
                            <a href="tel:+250784183352">+250 784 183 352</a>
                        </div>
                    </div>
                    <div class="fl-footer__contact-item">
                        <i class="fa fa-envelope"></i>
                        <div>
                            <a href="mailto:fairlawfirmltd@gmail.com">fairlawfirmltd@gmail.com</a>
                        </div>
                    </div>
                    <div class="fl-footer__contact-item">
                        <i class="fa fa-clock"></i>
                        <div>Mon - Fri: 09:00 - 17:00</div>
                    </div>
                </div>

            </div>

            <!-- Bottom Bar -->
            <div class="fl-footer__bottom">
                <div class="fl-footer__bottom-inner">
                    <p>&copy; 2026 Fair Law Firm LTD. <?= __('All rights reserved.') ?></p>
                    <div class="fl-footer__bottom-links">
                        <a href="about_us.php"><?= __('About') ?></a>
                        <span class="fl-footer__bottom-divider"></span>
                        <a href="contact.php"><?= __('Contact') ?></a>
                        <span class="fl-footer__bottom-divider"></span>
                        <span class="fl-footer__lang-switch">
                            <a href="?lang=en" class="fl-footer__lang <?= (($_SESSION['lang'] ?? 'en') === 'en') ? 'fl-footer__lang--active' : '' ?>">EN</a>
                            <span class="fl-footer__lang-sep">|</span>
                            <a href="?lang=fr" class="fl-footer__lang <?= (($_SESSION['lang'] ?? 'en') === 'fr') ? 'fl-footer__lang--active' : '' ?>">FR</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Float -->
    <a href="https://wa.me/message/CDM47NATCOISH1" target="_blank" rel="noopener" class="fl-whatsapp" aria-label="Chat on WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- Scroll to Top -->
    <button class="fl-scroll-top" id="flScrollTop" aria-label="Back to top">
        <i class="fa fa-chevron-up"></i>
    </button>

    <!-- ============ SCRIPTS ============ -->
    <script src="assets/vendors/jquery/jquery-3.7.0.min.js"></script>
    <script src="assets/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scroll to Top
        var btn = document.getElementById('flScrollTop');
        if (btn) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 400) {
                    btn.classList.add('fl-scroll-top--visible');
                } else {
                    btn.classList.remove('fl-scroll-top--visible');
                }
            });
            btn.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    });
    </script>

    <!-- Legacy JS (kept for backward compat on inner pages using old template classes) -->
    <script src="assets/vendors/owl-carousel/js/owl.carousel.min.js"></script>
    <script src="assets/vendors/jquery-magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/firdip.js"></script>
</body>
</html>
