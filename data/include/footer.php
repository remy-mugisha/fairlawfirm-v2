                  <!-- ==================== FOOTER ==================== -->
                  <div class="flf-footer">
                     <p>&copy; <?php echo date('Y'); ?> <a href="https://fairlawfirmltd.com/" target="_blank" rel="noopener">Fair Law Firm LTD</a><span class="flf-sep">&middot;</span>All rights reserved.</p>
                  </div>
               </div>
               <!-- end dashboard inner -->
            </div>
            <!-- end #content -->
         </div>
         <!-- end .inner_container -->
      </div>
      <!-- end .full_container -->



      <!-- ==================== SCRIPTS ==================== -->

      <!-- Core libraries: jQuery, Popper, Bootstrap -->
      <script src="js/jquery.min.js"></script>
      <script src="js/popper.min.js"></script>
      <script src="js/bootstrap.min.js"></script>

      <!-- UI components: select boxes, carousels, animations -->
      <script src="js/bootstrap-select.js"></script>
      <script src="js/owl.carousel.js"></script>
      <script src="js/animate.js"></script>

      <!-- Sidebar: custom scrollbar + toggle (custom.js binds #sidebarCollapse) -->
      <script src="js/perfect-scrollbar.min.js"></script>
      <script>
         var ps = new PerfectScrollbar('#sidebar');
      </script>
      <script src="js/custom.js"></script>

      <!-- Chart.js stack (used by analytics pages) -->
      <script src="js/Chart.min.js"></script>
      <script src="js/Chart.bundle.min.js"></script>
      <script src="js/utils.js"></script>
      <script src="js/analyser.js"></script>
      <script src="js/chart_custom_style1.js"></script>

      <!-- Mobile menu behaviour: close off-canvas sidebar on outside click / Esc -->
      <script>
      (function () {
         'use strict';
         var mq = window.matchMedia('(max-width: 1199px)');
         var sidebar = document.getElementById('sidebar');

         function closeMobileMenu() {
            if (mq.matches && sidebar.classList.contains('active')) {
               sidebar.classList.remove('active');
            }
         }

         document.addEventListener('click', function (e) {
            if (!mq.matches || !sidebar.classList.contains('active')) return;
            if (e.target.closest('#sidebar') || e.target.closest('#sidebarCollapse')) return;
            closeMobileMenu();
         });

         document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeMobileMenu();
         });
      })();
      </script>
   </body>

</html>
